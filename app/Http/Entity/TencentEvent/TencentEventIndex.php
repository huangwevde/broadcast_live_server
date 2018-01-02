<?php

namespace App\Http\Entity\TencentEvent;

use App\Http\Entity\Base;

class TencentEventIndex extends Base
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $table = 'tencent_event_index';

    public $timestamps = false;

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        $index_id = parent::store($data);

        if ($index_id) {
            $data['index_id'] = $index_id;
            // event_type 事件类型; 0 — 代表断流，1 — 代表推流，100 — 新的录制文件已生成，200 — 新的截图文件已生成
            switch ($data['event_type']) {
                case '0':
                case '1':
                    $ok = TencentEventPush::store($data);
                    break;
                case '100':
                    $ok = TencentEventRecord::store($data);
                    break;
                case '200':
                    $ok = TencentEventScreenshot::store($data);
                default:
                    break;
            }
            return true;
        }
        return false;
    }

}
