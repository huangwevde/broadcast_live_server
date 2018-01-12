<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Entity\ClientUser;
use App\Http\Entity\Live\LiveIndex;
use App\Http\Utils\TencentLive;
use App\Http\Utils\TencentLiveApi;
use App\Http\Entity\Live\LiveBranch;
use Illuminate\Http\Request;
use App\Http\Entity\Im\Group;
use App\Http\Entity\Live\LiveFiles;
use App\Http\Utils\Tools;
use App\Http\Utils\TLSSigAPI;
use Log;
use Session;

class LiveController extends Controller
{

    private $file_page_size = 10;

    /**
     * 直播列表
     * @param $cid
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = [
            'title' => $request->get('title', ''),
            'start_at' => $request->get('start_at', ''),
            'end_at' => $request->get('end_at', ''),
            'status' => $request->get('status', ''),
        ];
        $pageSize = $request->get('pageSize', 15);

        $data = LiveIndex::getListByClientId($this->client_id, $search, $pageSize);

        return $this->api($data);
    }

    /**
     * 
     * @param 
     * @return 
     */
    public function show(Request $request, $id)
    {
        $data = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id], LiveIndex::getDetailCol());
        $data['files'] = LiveFiles::getFilesByLiveId($id);

        return $this->api($data);
    }

    /**
     * 创建直播
     * @param $cid
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if (!Tools::custom_validate_array($data, ['title', 'start_at', 'end_at'])) {
            return $this->apiError('参数错误');
        }
        $data['client_id'] = $this->client_id;
        $data['stream_id'] = md5(time() . uniqid() . $this->client_id);
        $data['webcast_id'] = md5($data['stream_id']);
        $data['room_id'] = Group::createGroup('room' . '_' . $this->client_id . '_' . substr($data['webcast_id'], 8, 16));
        $data['organizer_join_url'] = url('/webcast/live/organizer-' . md5('organizer' . $data['webcast_id']));
        $data['panelist_join_url'] = url('/webcast/live/panelist-' . $data['webcast_id']);
        $data['assistant_join_url'] = url('/webcast/live/entry-' . $data['webcast_id']);
        $data['panelist_token'] = Tools::genRandString(6, '0123456789');
        $data['assistant_token'] = Tools::genRandString(6, '0123456789');
        $res = LiveIndex::store($data);

        if ($res) {
            return $this->api('创建成功');
        }

        return $this->apiError('创建失败');
    }

    /**
     * 
     * @param 
     * @return 
     */
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        // 获取直播
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        if (!$live) {
            return $this->apiError('直播不存在');
        }

        $data['updated_at'] = time();
        $res = LiveIndex::edit($id, $data);
        if ($res) {
            return $this->api('更新成功');
        }

        return $this->apiError('更新失败');
    }

    /**
     * 
     * @param 
     * @return 
     */
    public function destroy(Request $request, $id)
    {
        // 获取直播
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        if (!$live) {
            return $this->apiError('直播不存在');
        }
        $res = LiveIndex::edit($id, ['deleted' => 1, 'deleted_at' => time()]);
        if ($res) {
            return $this->api('删除成功');
        }

        return $this->apiError('删除失败');
    }

    /**
     * 直播页面
     * @param $id
     * @return mixed
     */
    public function livePush(Request $request, $id)
    {
        $branch = $request->get('branch', false);
        $liveApi = new TencentLiveApi();

        // 获取直播码 并允许直播
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        $err = $this->checkStartLive($live);
        if ($err) {
            return $this->apiError($err);
        }

        // 获取直播推流地址
        $bizId = env('TX_LIVE_BIZ_ID');
        $key = env('TX_LIVE_PUSH_KEY');
        if ($branch) {
            $push_stream_id = LiveBranch::getBranchStreamId($id);
        } else {
            $push_stream_id = $live['stream_id'];
        }
        $live['push_url'] = TencentLive::genPushUrl($bizId, $push_stream_id, $key, strtotime('+1 day'));
        $live['play_url'] = TencentLive::genPlayUrl($bizId, $live['stream_id']);

        // 获取直播课件
        $live['files'] = LiveFiles::getFilesByLiveId($id, $this->file_page_size);
        // 直播聊天室初始化身份信息
        $live['im_login_info'] = $this->genImLoginInfo($id);

        return $this->api($live);
    }

    /**
     * 开始直播
     * @param $id
     * @return mixed
     */
    public function startLive(Request $request, $id)
    {
        // 获取直播码 并允许直播推流
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        $err = $this->checkStartLive($live);
        if ($err) {
            return $this->apiError($err);
        }

        // 允许推流
        $stream_id = $live['stream_id'];
        $apiRes = $liveApi->setLiveStatus($streamId, 1);

        // 更新直播状态 201直播中
        if (!empty($apiRes['ret']) && $apiRes['ret'] == 0) {
            LiveIndex::edit($id, ['status' => 201, 'updated_at' => time()]);
        } else {
            return apiError(!empty($apiRes['message']) ? $apiRes['message'] : '开启出错');
        }
    }

    /**
     * 结束直播
     * @param $id
     * @return mixed
     */
    public function stopLive(Request $request, $id)
    {
        // 获取直播码 并关闭直播
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        if (!$live) {
            return $this->apiError('直播不存在');
        }

        // 关闭推流 0 表示禁用 2 表示断流 超过直播结束时间的直接禁用
        $stream_id = $live['stream_id'];
        $apiRes = $liveApi->setLiveStatus($streamId, time() >= $live['end_at'] ? 0 : 2);

        // 更新直播状态 301直播结束
        if (!empty($apiRes['ret']) && $apiRes['ret'] == 0) {
            LiveIndex::edit($id, ['status' => 301, 'updated_at' => time()]);
        } else {
            return apiError(!empty($apiRes['message']) ? $apiRes['message'] : '关闭出错');
        }
    }

    /**
     * 获取播放url
     * @param 
     * @return 
     */
    public function getPlayUrl(Request $request, $id)
    {
        // 获取直播
        $live = LiveIndex::getLive(['id' => $id, 'client_id' => $this->client_id]);
        if (!$live) {
            return $this->apiError('直播不存在');
        }
        $res = TencentLive::genPlayUrl(env('TX_LIVE_BIZ_ID'), $live['stream_id']);
        return $this->api($res);
    }

    /**
     * 
     * @param array $live
     * @return string $err
     */
    protected function checkStartLive($live)
    {
        $err = '';
        if (!$live) {
            $err = '直播不存在';
        }
        if (time() <= $live['start_at']) {
            $err = '未到直播开始时间';
        }
        if (time() >= $live['end_at']) {
            $err = '超出直播结束时间';
        }
        return $err;
    }

    /**
     * 
     * @param array $live
     * @return string $err
     */
    protected function genImLoginInfo($id)
    {
        $tlsApi = new TLSSigAPI();
        $sig = $tlsApi->genSig($id . '_organizer');

        $loginInfo = [
            'sdkAppID' => env('TX_IM_SDK_APPID', ''),
            'appIDAt3rd' => env('TX_IM_SDK_APPID', ''),
            'accountType' => env('TX_IM_ACCOUNT_TYPE', ''),
            'identifier' => $id . '_organizer',
            'userSig' => $sig,
        ];
        return $loginInfo;
    }

}
