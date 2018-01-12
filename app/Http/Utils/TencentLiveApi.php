<?php

namespace App\Http\Utils;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;


class TencentLiveApi
{

    private $api = '';
    private $appId = '';
    private $apiKey = '';
    private $interface = '';

    public function __construct()
    {
        $this->api = 'http://fcgi.video.qcloud.com/common_access';
        $this->appId = env('TX_LIVE_APP_ID');
        $this->apiKey = env('TX_LIVE_API_KEY');
    }

    /**
     * 设置直播码状态
     * @param $streamId
     * @param $status 0 表示禁用，1 表示允许推流，2 表示断流
     */
    public function setLiveStatus($streamId, $status)
    {
        $this->interface = 'Live_Channel_SetStatus';
        $param = [
            'Param.s.channel_id' => $streamId,
            'Param.n.status' => $status,
        ];
        $this->send($param);
    }

    /**
     * 发送直播相关API请求
     * @param $param
     * @return mixed
     */
    public function send($param)
    {
        $query['appid'] = $this->appId;
        $query['interface'] = $this->interface;
        $query = array_merge($query, $param);
        $query['t'] = time();
        $query['sign'] = TencentLive::genTxSign($query['t']);
        $exec = $this->api . '?' . http_build_query($query);
        return json_decode(HTTP::get($exec));
    }
}
