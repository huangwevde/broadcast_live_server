<?php

namespace App\Http\Entity\Live;

use App\Http\Entity\Base;
use App\Http\Utils\TencentLive;

class LiveIndex extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'live_index';

    public $timestamps = false;

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $live_id = parent::store($data);
        // 保存直播课件
        if ($live_id && !empty($data['file_id'])) {
            LiveFiles::store(['live_id' => $live_id, 'file_id' => $data['file_id']]);
        }
        return $live_id;
    }

    /**
     * 更新
     * @param $id ID
     * @param $data 内容
     * @return array
     */
    public static function edit($id, $data)
    {
        $live_index = LiveIndex::find($id);
        $live_index->attributes = self::getFilter($data);
        $res = $live_index->save();
        // 保存直播课件
        if ($res && !empty($data['file_id'])) {
            LiveFiles::edit($id, ['file_id' => $data['file_id']]);
        }
        return $res;
    }

    /**
     * get live list
     * @param string $client_id
     * @return array
     */
    public static function getListByClientId($client_id, $search, $pageSize, $order = [])
    {
        $builder = LiveIndex::where('client_id', $client_id);

        // 直播标题
        if (!empty($search['title'])) {
            $builder->where('title', 'like', '%'.$search['title'].'%');
        }
        // 直播时间
        if (!empty($search['start_at']) && !empty($search['end_at'])) {
            $builder->where('start_at', '>=', $search['start_at']);
            $builder->where('end_at', '<=', $search['end_at']);
        }
        // 直播状态
        if (!empty($search['status'])) {
            $builder->where('status', $search['status']);
        }

        $builder->select(self::getListCol());
        $builder->orderBy('updated_at', 'desc');
        $builder->orderBy('created_at', 'desc');
        $result = $builder->paginate($pageSize)->toArray();

        // return self::getPushUrl($result);
        return $result;
    }

    /**
     * get live by $where
     * @param array $where
     * @return bool
     */
    public static function getLive($where, $fields = '*') 
    {
        $live = LiveIndex::where(self::getFilter($where))->first();
        if ($live) {
            $result = $live->toArray();
            // files

            return $result;
        }
        return false;
    }

    /**
     * get lives by $where
     * @param array $where
     * @return bool
     */
    public static function getLives($where, $fields = '*') 
    {
        $lives = LiveIndex::where(self::getFilter($where))->select($fields)->get();
        if ($lives) {
            return $lives->toArray();
        }
        return false;
    }

    /**
     * get pushUrl & get playUrl
     * @param array $data
     * @return bool
     */
    protected static function getPushUrl(&$data)
    {
        $bizId = env('TX_LIVE_BIZ_ID');
        $pushKey = env('TX_LIVE_PUSH_KEY');
        $time = strtotime('+1 day');
        if (!empty($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['push_url'] = TencentLive::genPushUrl($bizId, $value['stream_id'], $pushKey, $time);
                $data['data'][$key]['play_url'] = TencentLive::genPlayUrl($bizId, $value['stream_id']);
            }
        }
        return $data;
    }

    /**
     * 直播列表返回字段
     *
     * @return array
     */
    protected static function getListCol()
    {
        return [
            'id',
            'client_id',
            'webcast_id',
            'title',
            'sub_title',
            'description',
            'type',
            'start_at',
            'end_at',
            'cover',
            'status',
        ];
    }

    /**
     * 直播详情返回字段
     *
     * @return array
     */
    protected static function getDetailCol()
    {
        return self::getListCol();
    }

    /**
     * 开启直播页面返回字段
     *
     * @return array
     */
    protected static function getPushCol()
    {
        return '*';
    }

}
