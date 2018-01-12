<?php

namespace App\Http\Entity\Im;

use App\Http\Entity\Base;
use App\Http\Utils\TLSSigAPI;
use App\Http\Utils\Http;
use Illuminate\Http\Request;

class Group
{

    protected static $url = 'https://console.tim.qq.com/v4/group_open_http_svc/';

    /**
    * 创建群组
    * @return mixed
    */
    public static function createGroup($room_id) 
    {
        // https://console.tim.qq.com/v4/group_open_http_svc/create_group?usersig=xxx&identifier=admin&sdkappid=88888888&random=99999999&contenttype=json
        // {
        //     "Owner_Account": "leckie", // 群主的UserId（选填）
        //     "Type": "Public", // 群组类型：Private/Public/ChatRoom/AVChatRoom/BChatRoom（必填）
        //     "GroupId": "MyFirstGroup", //用户自定义群组ID（选填）
        //     "Name": "TestGroup"   // 群名称（必填）
        // }
        $body = [
            'Type' => 'AVChatRoom',
            'GroupId' => $room_id,
            'Name' => $room_id,
        ];

        $result = self::doRestApi('create_group', $body);

        return $result['ActionStatus'] == 'OK' ? $result['GroupId'] : 0;
    }

    /**
    * 
    * @param 
    * @return mixed
    */
    protected static function doRestApi($api_name, $body) 
    {
        $tlsApi = new TLSSigAPI();
        $sig = $tlsApi->genSig('admin');

        switch ($api_name) {
            case 'create_group':
            case 'get_appid_group_list':
            case 'add_group_member':
            case 'delete_group_member':
            case 'get_joined_group_list':
                $ext_str = "?" . http_build_query(array(
                    "usersig" => $sig,
                    "identifier" => 'admin',
                    "sdkappid" => config('tencentIm.im_tls.app_id', '1400057813'),
                    "random" => str_random(8),
                    'contenttype' => 'json',
                ));
                break;
            default:
                break;
        }
        $result = Http::doRequest(self::$url . $api_name . $ext_str, Request::METHOD_POST, [], ['body' => json_encode($body, true)]);
        return json_decode($result, true);
    }

}
