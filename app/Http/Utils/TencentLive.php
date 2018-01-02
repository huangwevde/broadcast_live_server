<?php

namespace App\Http\Utils;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;


class TencentLive
{

    public static $client = null;

    /**
    * 腾讯云安全签名
    * @param int $t 过期时间
    * @param string $sign
    * @return bool
    */
    public static function genTxSign($txTime)
    {
        return md5(env('TX_LIVE_API_KEY') . $txTime);
    }

    /**
    * 腾讯云安全签名认证
    * @param int $t 过期时间
    * @param string $sign
    * @return bool
    */
    public static function checkTxSign($t, $sign)
    {
        return md5(env('TX_LIVE_API_KEY') . $t) === $sign ? true : false;
    }

    /**
    * 获取推流地址
    * 如果不传key和过期时间，将返回不含防盗链的url
    * @param bizId 腾讯云分配到的bizid
    * @param streamId 用来区别不同推流地址的唯一id
    * @param key 安全密钥
    * @param time 过期时间 sample 2016-11-12 12:00:00
    * @return String url 
    */
    public function genPushUrl($bizId, $streamId, $key = null, $time = null)
    {
        if ($key && $time)
        {
            $txTime = strtoupper(base_convert($time, 10, 16));
            $livecode = $bizId . "_" . $streamId; //直播码
            $txSecret = md5($key . $livecode . $txTime);
            $ext_str = "?" . http_build_query(array(
                "bizid" => $bizId,
                "txSecret" => $txSecret,
                "txTime" => $txTime
            ));
        }
        return "rtmp://" . $bizId . ".livepush.myqcloud.com/live/" . $livecode . (isset($ext_str) ? $ext_str : "");
    }

    /**
    * 获取播放地址
    * @param bizId 腾讯云分配到的bizid
    * @param streamId 用来区别不同推流地址的唯一id
    * @return String url 
    */
    public function genPlayUrl($bizId, $streamId)
    {
        $livecode = $bizId . "_" . $streamId; //直播码
        return array(
            "rtmp://" . $bizId . ".liveplay.myqcloud.com/live/" . $livecode,
            "http://" . $bizId . ".liveplay.myqcloud.com/live/" . $livecode . ".flv",
            "http://" . $bizId . ".liveplay.myqcloud.com/live/" . $livecode . ".m3u8"
        );
    }

}
