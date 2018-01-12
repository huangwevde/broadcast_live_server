<?php

namespace App\Http\Entity\Im;

use App\Http\Entity\Base;

class ImEventIndex extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = '';

    public $timestamps = false;

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        if ($data) {
            // CallbackCommand 回调命令 事件类型
            switch ($data['CallbackCommand']) {
                case 'Group.CallbackBeforeSendMsg':
                case 'Group.CallbackAfterSendMsg':
                    // 聊天室消息事件
                    $ok = self::storeMsg($data);
                    break;
                case 'Group.CallbackAfterNewMemberJoin':
                case 'Group.CallbackAfterMemberExit':
                    $ok = self::storeState($data);
                    break;
                case '':
                    
                default:
                    break;
            }
            return true;
        }
        return false;
    }

    /**
     * store im message event
     * @param array $data
     * @return bool
     */
    protected static function storeMsg($data)
    {
        $params = [
            'room_id' => $data['GroupId'],
            'event_type' => $data['CallbackCommand'] == 'Group.CallbackBeforeSendMsg' ? 1 : 2,
            'from_account' => $data['From_Account'],
            'operator_account' => !empty($data['Operator_Account']) ? $data['Operator_Account'] : $data['From_Account'],
            'msg_type' => $data['MsgBody'][0]['MsgType'],
            'msg_content' => json_encode($data['MsgBody'][0]['MsgContent'], JSON_UNESCAPED_UNICODE),
            'random' => !empty($data['Random']) ? $data['Random'] : '0',
            'client_ip' => $data['ClientIP'],
            'platform' => $data['OptPlatform'],
            'event_time' => time(),
        ];
        return ImEventMessage::store($params);
    }

    /**
     * store im member state event
     * @param array $data
     * @return bool
     */
    protected static function storeState($data)
    {
        $params = [
            'room_id' => $data['GroupId'],
            'event_type' => $data['CallbackCommand'] == 'Group.CallbackAfterNewMemberJoin' ? 1 : 2,
            'new_account' => $data['NewMemberList'][0]['Member_Account'],
            // 'operator_account' => $data['Operator_Account'],  // 操作者成员
            "join_type" => $data['JoinType'],  // 入群方式：Apply（申请入群）；Invited（邀请入群）。
            'client_ip' => $data['ClientIP'],
            'platform' => $data['OptPlatform'],
            'event_time' => time(),
        ];
        return ImEventState::store($params);
    }

}
