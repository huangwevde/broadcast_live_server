<?php

namespace App\Http\Controllers\Im;

use App\Http\Controllers\Controller;
use App\Http\Utils\TLSSigAPI;
use App\Http\Utils\Tools;
use App\Http\Utils\Http;
use Illuminate\Http\Request;
use Log;

class GroupController extends Controller
{

    protected $url = 'https://console.tim.qq.com/v4/group_open_http_svc/';

    /**
    * 获取群列表
    * @param 
    * @return mixed
    */
    public function index(Request $request) 
    {
        // https://console.tim.qq.com/v4/group_open_http_svc/get_appid_group_list?usersig=xxx&identifier=admin&sdkappid=88888888&random=99999999&contenttype=json
        $result = $this->doRestApi('get_appid_group_list', []);
        dd($result);
    }

    /**
    * 创建群组
    * @return mixed
    */
    public function createGroup(Request $request) 
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
            'GroupId' => 'testGroup02',
            'Name' => '测试群组01',
        ];
        $result = $this->doRestApi('create_group', $body);
        dd($result);
    }

    /**
    * 加入群组
    * @param 
    * @return mixed
    */
    public function addGroup(Request $request) 
    {
        // https://console.tim.qq.com/v4/group_open_http_svc/add_group_member?usersig=xxx&identifier=admin&sdkappid=88888888&random=99999999&contenttype=json
        // {
        //     "GroupId": "@TGS#2J4SZEAEL",   // 要操作的群组（必填）    
        //     "Silence": 1,   // 是否静默加人（选填）       
        //     "MemberList": [  // 一次最多添加500个成员       
        //     {          
        //         "Member_Account": "tommy"  // 要添加的群成员ID（必填）        
        //     },        
        //     {           
        //         "Member_Account": "jared"       
        //     }]
        // }
        $MemberList[] = [
            'Member_Account' => 'tester01',
        ];
        $body = [
            'GroupId' => 'testGroup01',
            'Silence' => 1,
            'MemberList' => $MemberList,
        ];
        $result = $this->doRestApi('add_group_member', $body);
        dd($result);
    }

    /**
    * 删除群组成员
    * @param 
    * @return mixed
    */
    public function delGroup(Request $request) 
    {
        // https://console.tim.qq.com/v4/group_open_http_svc/delete_group_member?usersig=xxx&identifier=admin&sdkappid=88888888&random=99999999&contenttype=json
        // {
        //     "GroupId": "@TGS#2J4SZEAEL",   //要操作的群组（必填）
        //     "Silence": 1,   // 是否静默删除（选填）
        //     "MemberToDel_Account": [   // 要删除的群成员列表，最多500个
        //         "tommy",
        //         "jared"
        //     ]
        // }
        $members = [
            'tester01',
        ];
        $body = [
            'GroupId' => 'testGroup01',
            'Silence' => 1,
            'MemberToDel_Account' => $members,
        ];
        $result = $this->doRestApi('delete_group_member', $body);
        dd($result);
    }

    /**
    * 获取用户所加入的群组
    * @param 
    * @return mixed
    */
    public function getJoinedGroup(Request $request) 
    {
        // https://console.tim.qq.com/v4/group_open_http_svc/get_joined_group_list?usersig=xxx&identifier=admin&sdkappid=88888888&random=99999999&contenttype=json
        // {
        //     "Member_Account": "leckie", 
        //     "Limit": 10,  // 拉取多少个，不填标识拉取全部
        //     "Offset": 0  // 从第多少个开始拉取
        // }
        $members = [
            'tester01',
        ];
        $body = [
            'Member_Account' => 'tester01',
            'Limit' => 10,
            'Offset' => 0,
        ];
        $result = $this->doRestApi('get_joined_group_list', $body);
        dd($result);
    }

    /**
    * 
    * @param 
    * @return mixed
    */
    protected function doRestApi($api_name, $body) 
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
                    "sdkappid" => env('TX_IM_SDK_APPID'),
                    "random" => str_random(8),
                    'contenttype' => 'json',
                ));
                break;
            default:
                break;
        }
        $result = Http::doRequest($this->url . $api_name . $ext_str, Request::METHOD_POST, [], ['body' => json_encode($body, true)]);
        return $result;
    }

}
