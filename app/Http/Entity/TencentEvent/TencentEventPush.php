<?php

namespace App\Http\Entity\TencentEvent;

use App\Http\Entity\Base;

class TencentEventPush extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'tencent_event_push';

    public $timestamps = false;

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        return parent::store($data);
    }

}
