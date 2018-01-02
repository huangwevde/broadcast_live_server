<?php

namespace App\Http\Entity;

class MixStream extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public $timestamps = false;

    /**
    * 混流 input list
    * @param int $t 过期时间
    * @param string $sign
    * @return bool
    */
    protected function genInputStreamList()
    {
        $stream_1 = [
            'input_stream_id' => '18167_6a25404a08',
            'layout_params' => [
                'image_layer' => 1,
            ],
        ];
        $stream_2 = [
            'input_stream_id' => '18167_65b12b4b75',
            'layout_params' => [
                'image_layer' => 2,
            ],
        ];
        $input_stream_list = [$stream_1, $stream_2];
        return $input_stream_list;
    }

    /**
    * 混流 body
    * @param string $stream_id 流ID
    * @param string $input_stream_list 混流List
    * @return string
    */
    protected function genMixBody($stream_session_id, $stream_id, $input_stream_list)
    {
        $body = [];
        $body['timestamp'] = $body['eventId'] = time();
        $interface['interfaceName'] = 'mix_streamv2';
        $interface['para'] = [
            'app_id' => env('TX_LIVE_APP_ID'),
            'interface' => 'mix_streamv2.start_mix_stream_advanced',
            'mix_stream_session_id' => $stream_session_id,
            'output_stream_id' => $stream_id,
            'output_stream_type' => 0,
            'input_stream_list' => $input_stream_list,
            'mix_stream_template_id' => 10,
        ];
        $body['interface'] = $interface;
        return json_encode($body, true);
    }

}
