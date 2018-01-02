<?php

namespace App\Http\Utils;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;


class Tools
{

    public static $client = null;

    /**
    * 数组健值验证
    * @param array $arr
    * @param array $volidate_key
    * @return bool
    */
    public static function custom_volidate_array($arr, $volidate_arr)
    {
        foreach ($volidate_arr as $val) {
            if (!array_key_exists($val, $arr)) {
                return false;
            }
        }
        return true;
    }

    /**
    * 生产盐值
    * @return String
    */
    public static function custom_function_for_salt()
    {
        return $salt = substr(md5(uniqid(rand(), true)), 0, 22);
    }

    /**
     * 生成指定长度随机数
     * @param $len
     * @return string
     */
    public static function genRandString($len)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str ='';
        for ( $i = 0; $i < $len; $i++ ) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

}
