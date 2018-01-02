<?php

namespace App\Http\Entity\Im;

use App\Http\Entity\Base;

class Group extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = '';

    public $timestamps = false;

}
