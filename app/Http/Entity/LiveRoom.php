<?php

namespace App\Http\Entity;

class LiveRoom extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    protected $table = 'live_room';

    public $timestamps = false;

    /**
     * 获取cid对应的直播码
     * @param $cid
     * @return mixed
     */
    public static function getStreamId($cid)
    {
        return self::where('client_user', $cid)->value('stream_id');
    }

}
