<?php

namespace App\Http\Entity;

class Variable extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'status',
    ];

    protected $table = 'variable';

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

    /**
     * 根据username获取client_id
     * @param array $where
     * @param array $data
     * @return bool
     */
    public static function getClientId($username)
    {
        return self::where('username', $username)->value('id');
    }

}
