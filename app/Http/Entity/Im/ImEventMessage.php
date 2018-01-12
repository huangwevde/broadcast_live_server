<?php

namespace App\Http\Entity\Im;

use App\Http\Entity\Base;

class ImEventMessage extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'im_event_message';

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
