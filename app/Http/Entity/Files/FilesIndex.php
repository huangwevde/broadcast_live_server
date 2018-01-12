<?php

namespace App\Http\Entity\Files;

use App\Http\Entity\Base;

class FilesIndex extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'files_index';

    public $timestamps = false;

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        $data['created_at'] = time();
        return parent::store($data);
    }

    /**
     * getBy
     * @param array $data
     * @return bool
     */
    public static function getFileById($id)
    {
        $file = FilesIndex::find($id);
        if ($file) {
            return $file->toArray();
        }
        return false;
    }

    /**
     * getBy
     * @param array $data
     * @return bool
     */
    public static function getBy($where)
    {
        $file = FilesIndex::where(self::getFilter($where))->first();
        if ($file) {
            return $file->toArray();
        }
        return false;
    }

}
