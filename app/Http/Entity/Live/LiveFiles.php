<?php

namespace App\Http\Entity\Live;

use App\Http\Entity\Base;

class LiveFiles extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'live_files';

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
     * 更新
     * @param $id ID
     * @param $data 内容
     * @return array
     */
    public static function edit($live_id, $data)
    {
        LiveFiles::where('live_id', $live_id)->delete();
        $data['live_id'] = $live_id;
        $data['created_at'] = time();
        $res = self::store($data);
        // $res = LiveFiles::where('live_id', $live_id)->update($data);
        return $res;
    }

    /**
     * get live files
     * @param int $live_id
     * @return bool
     */
    public static function getFilesByLiveId($live_id, $pageSize = 0)
    {
        $builder = LiveFiles::where('live_id', $live_id);
        $builder->join('files_index', 'files_index.id', '=', 'live_files.file_id');
        $builder->where('live_files.deleted', 0);
        $builder->orderBy('files_index.created_at', 'desc');
        $builder->select('files_index.*');

        if ($pageSize) {
            $result = $builder->paginate($pageSize)->toArray();
        } else {
            $result = $builder->get()->toArray();
        }

        return $result;
    }

}
