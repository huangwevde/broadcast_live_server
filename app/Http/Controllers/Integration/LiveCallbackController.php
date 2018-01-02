<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Utils\Tools;
use Illuminate\Http\Request;
use Log;
use App\Http\Entity\TencentEvent\TencentEventIndex;

class LiveCallbackController extends Controller
{

    /**
    * 腾讯云直播 事件消息通知
    * @param string t 有效时间
    * @param string sign 安全签名
    * @param int event_type 事件类型; 0 — 代表断流，1 — 代表推流，100 — 新的录制文件已生成，200 — 新的截图文件已生成
    * @param string stream_id 直播码
    * @param string channel_id 同直播码
    * @return mixed
    */
    public function index(Request $request) 
    {
        $data = $request->all();
        Log::info('腾讯云回调：');
        Log::info($data);
        
        // 保存事件消息
        TencentEventIndex::store($data);
    }

}
