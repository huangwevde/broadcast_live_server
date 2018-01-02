<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Utils\Tools;
use App\Http\Utils\TencentLive;

class IndexController extends Controller
{
    public function index() 
    {
        $bizid = env('TX_LIVE_BIZ_ID');
        $stream_id = 'dxy_broadcast_test001';
        $key = env('TX_LIVE_PUSH_KEY');
        $txTime = strtotime('+1 day');
        $push_url = TencentLive::genPushUrl($bizid, $stream_id, $key, $txTime);
        $play_url = TencentLive::genPlayUrl($bizid, $stream_id);
        dd($play_url);
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
        return TencentLive::genPushUrl($bizid, $stream_id, $key, $txTime);
    }

    /**
    * 获取播放地址
    * @param bizId 腾讯云分配到的bizid
    * @param streamId 用来区别不同推流地址的唯一id
    * @return String url 
    */
    public function getPlayUrl($bizId, $streamId)
    {
        return TencentLive::genPlayUrl($bizid, $stream_id);
    }

}
