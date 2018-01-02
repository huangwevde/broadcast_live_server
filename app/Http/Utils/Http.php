<?php

namespace App\Http\Utils;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;


class Http
{

    public static $client = null;

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

    public static function get($url, $params = [])
    {
        $data = self::doRequest($url, Request::METHOD_GET, $params);
        return $data;
    }

    public static function doRequest($url, $method = Request::METHOD_GET, array $data = [], array $options = [])
    {
        if ($data) {
            if ($method == Request::METHOD_GET) {
                $url .= '?' . http_build_query($data);
            }
            if (in_array($method, [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_DELETE])) {
                $options['form_params'] = $data;
            }
        }

        $options['verify'] = false;
        $client = self::getClientInstance();

        try {
            $response = $client->request($method, $url, $options);
        } catch (\Exception $e) {
            $response = false;
        }
        if ($response) {
            $res = $response->getBody()->getContents();
        } else {
            $res = null;
        }
        return $res;
    }

    protected static function getClientInstance()
    {
        if (self::$client === null) {
            self::$client = new HttpClient();
        }
        return self::$client;
    }

}
