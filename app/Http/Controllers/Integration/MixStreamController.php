<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Utils\Http;
use App\Http\Utils\TencentLive;
use App\Http\Entity\MixStream;

class MixStreamController extends Controller
{

    private $interface_name = 'mix_streamv2.start_mix_stream_advanced';

    public function index() 
    {
        $stream_session_id = '18167_6a25404a08_test01';
        $url = 'http://fcgi.video.qcloud.com/common_access';
        $appid = env('TX_LIVE_APP_ID');
        $txTime = strtotime('+1 minute');
        $sign = TencentLive::genTxSign($txTime);
        $ext_str = "?" . http_build_query(array(
            "appid" => $appid,
            "interface" => 'mix_streamv2.start_mix_stream_advanced',
            "t" => $txTime,
            "sign" => $sign,
        ));
        $input_stream_list = MixStream::genInputStreamList();
        $body = MixStream::genMixBody($stream_session_id, '18167_6a25404a08', $input_stream_list);
        
        $result = Http::doRequest($url . $ext_str, Request::METHOD_POST, [], ['body' => $body]);
        dd($result);
    }

}
