<?php

namespace App\Http\Entity;

class ClientUser extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'status',
    ];

    protected $table = 'client_user';

    public $timestamps = false;

    /**
     * 获取hash加密密码
     * @param array $where
     * @param array $data
     * @return bool
     */
    public static function getHashPassWord($where)
    {
        return self::where($where)->value('password');
    }

}
