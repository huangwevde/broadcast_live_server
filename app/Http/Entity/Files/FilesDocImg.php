<?php

namespace App\Http\Entity\Files;

use App\Http\Entity\Base;

class FilesDocImg extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'files_doc_img';

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
    public static function getImgsByFid($fid)
    {
        $imgs = FilesDocImg::where(['fid' => $fid])->select('fid', 'page_number', 'path')->orderBy('page_number')->get();
        if ($imgs) {
            return $imgs->toArray();
        }
        return false;
    }

}
