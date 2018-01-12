<?php

namespace App\Http\Entity\LiveEvent;

use App\Http\Entity\Base;

class LiveEventPush extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'live_event_push';

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
