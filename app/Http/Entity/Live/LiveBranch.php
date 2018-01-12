<?php

namespace App\Http\Entity\Live;

use App\Http\Entity\Base;
use App\Http\Utils\TencentLive;

class LiveBranch extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'live_branch';

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

    /**
     * get branch stream_id
     * @param int $live_id
     * @return string
     */
    public static function getBranchStreamId($live_id)
    {
        return LiveBranch::where('live_id', $live_id)->valur('stream_id');
    }

}
