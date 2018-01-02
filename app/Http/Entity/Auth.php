<?php

namespace App\Http\Entity;

use App\Http\Utils\Tools;
use App\Http\Utils\Http;

class Auth extends Base
{

    /**
    * 用户名、密码调用api时的身份验证
    * @param array $request
    * @return bool
    */
    public static function apiUserCheck($request)
    {
        // $password = 'dxy123123123';
        // $options = [
        //     // 'salt' => Tools::custom_function_for_salt(),
        //     'cost' => 12 // the default cost is 10
        // ];
        // $hash = password_hash($password, PASSWORD_DEFAULT, $options);
        $data = $request->all();
        if (!Tools::custom_volidate_array($data, ['username', 'password'])) {
            return false;
        }
        $hash = ClientUser::where(['username' => $data['username']])->value('password');
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
        if (!Tools::custom_volidate_array($data, ['t', 'sign'])) {
            return false;
        }
        // 判断时间已经是否过期，腾讯云的消息通知的默认过期时间是10分钟
        if (time() > $data['t']) {
            return false;
        }
        return Http::checkTxSign($data['t'], $data['sign']);
    }

}
