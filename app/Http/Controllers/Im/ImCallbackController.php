<?php

namespace App\Http\Controllers\Im;

use App\Http\Controllers\Controller;
use App\Http\Utils\Tools;
use Illuminate\Http\Request;
use App\Http\Utils\BizLog;
use App\Http\Entity\Im\ImEventIndex;

class ImCallbackController extends Controller
{

    private $app_id;

    public function __construct()
    {
        $this->app_id = config('tencentIm.im_tls.app_id', '1400057813');
    }

    /**
    * 腾讯云通信 事件消息通知
    * @param string CallbackCommand 回调命令 回调类型
    * @param string ClientIP 客户端IP
    * @param string OptPlatform 设备类型，分为Windows，Web，Android，iOS，Mac和Unknown
    * @param string SdkAppid 在云通讯申请的Appid
    * @param string From_Account 用户的ID
    * @param string GroupId 房间ID
    * @param array MsgBody 消息体
    * @return mixed
    */
    public function index(Request $request) 
    {
        $data = $request->all();
        BizLog::info('腾讯云通信回调：');
        BizLog::info($data);

        // 验证Appid
        if (!empty($data['SdkAppid']) && $data['SdkAppid'] == $this->app_id) {
            // 保存事件消息
            ImEventIndex::store($data);
        }
        return ['ActionStatus' => 'OK', 'ErrorInfo' => '', 'ErrorCode' => 0];
    }

}
