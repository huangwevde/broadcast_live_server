<?php

namespace App\Http\Entity;

use App\Http\Utils\Tools;
use App\Http\Utils\Http;
use Illuminate\Support\Facades\App;

class Auth extends Base
{

    /**
     * 用户名、密码调用api时的身份验证
     * @param array $request
     * @return bool
     */
    public static function apiUserCheck($request)
    {
        if (env('APP_ENV') == 'dev') {
            $data = [
                'username' => $request->get('username'),
                'password' => $request->get('password'),
            ];
        } else {
            $data = $request->all();
        }

        if (!Tools::custom_validate_array($data, ['username', 'password'])) {
            return false;
        }

        $hash = ClientUser::where(['username' => $data['username']])->value('password');
        if (is_null($hash)) {
            return false;
        }

        if (password_verify($data['password'], $hash)) {
            return true;
        }
        return false;
    }

    /**
     * 腾讯直播云回调sign验证
     * @param array $request
     * @return bool
     */
    public static function txCallbackCheck($request)
    {
        $data = $request->all();
        // 签名验证
        if (!Tools::custom_validate_array($data, ['t', 'sign'])) {
            return false;
        }
        // 判断时间已经是否过期，腾讯云的消息通知的默认过期时间是10分钟
        if (time() > $data['t']) {
            return false;
        }
        return Http::checkTxSign($data['t'], $data['sign']);
    }

}
