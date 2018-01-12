<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Entity\ClientUser;
use App\Http\Entity\LiveRoom;
use App\Http\Utils\TencentLiveApi;
use App\Http\Utils\Tools;
use App\Http\Utils\TencentLive;
use App\Http\Entity\Variable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IndexController extends Controller
{

    /**
     * 获取配置信息
     * @param Request $request
     * @return mixed
     */
    public function getVariableInfo(Request $request)
    {
        
    }

    /**
     * 获取用户的身份验证信息
     * @param Request $request
     * @return mixed
     */
    private function getUserInfo(Request $request)
    {
        if (App::environment('dev')) {
            $data = [
                'username' => $request->header('username'),
                'password' => $request->header('password'),
            ];
        } else {
            $data = $request->all();
        }

        return ClientUser::where('username', $data['username'])->first();
    }


    public function index(Request $request)
    {
        $userInfo = $this->getUserInfo($request);
        $bizId = env('TX_LIVE_BIZ_ID');
        $streamId = LiveRoom::getStreamId($userInfo->id);
        $key = env('TX_LIVE_PUSH_KEY');

        return $this->startLive($userInfo->id);

        // $pushUrl = $this->createPushUrl($bizId, $streamId, $key);
        // $playUrl = $this->getPlayUrl($bizId, $streamId);
        // dd($pushUrl, $playUrl);
    }

    /**
     * 主播开始直播
     * @param $cid
     * @return \Illuminate\Http\JsonResponse
     */
    public function startLive($cid)
    {
        $liveApi = new TencentLiveApi();

        // 获取直播码 并允许直播
        $streamId = LiveRoom::getStreamId($cid);
        $apiRes = $liveApi->setLiveStatus($streamId, 1);

        // 获取直播推流地址
        $bizId = env('TX_LIVE_BIZ_ID');
        $pushKey = env('TX_LIVE_PUSH_KEY');
        $pushUrl = $this->createPushUrl($bizId, $streamId, $pushKey);

        return response()->json(['code' => 200, 'msg' => 'ok!', 'data' => ['push_url' => $pushUrl]]);
    }

    /**
     * 生产推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param bizId 腾讯云分配到的bizid
     * @param streamId 用来区别不同推流地址的唯一id
     * @param key 安全密钥
     * @param time 过期时间 sample 2016-11-12 12:00:00
     * @return String url
     */
    public function createPushUrl($bizId, $streamId, $key = null, $time = null)
    {
        if (is_null($time)) {
            $time = strtotime('+1 day');
        }
        return TencentLive::genPushUrl($bizId, $streamId, $key, $time);
    }

    /**
     * 获取播放地址
     * @param bizId 腾讯云分配到的bizid
     * @param streamId 用来区别不同推流地址的唯一id
     * @return String url
     */
    public function getPlayUrl($bizId, $streamId)
    {
        return TencentLive::genPlayUrl($bizId, $streamId);
    }

}
