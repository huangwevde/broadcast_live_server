<?php

namespace App\Http\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Http\Utils\Tools;

class Base extends Model
{
    public static function boot()
    {
        parent::boot();
        // static::created(function ($model) {
        //     Log::info($model->getTableName());
        // });

        // static::updated(function ($model) {
        //     Log::info($model->getTableName());
        // });

        // static::deleted(function ($model) {
        //     Log::info($model->getTableName());
        // });

        // static::saved(function ($model) {
        //     Log::info($model->getTableName());
        // });
    }

    /**
     * Get Model Table Name
     * @return string
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * Get Filter Columns
     * @param array $data
     * @param array $fields
     * @return array
     */
    public static function getFilter(array $data, array $fields = null)
    {
        // TODO
        if ($fields) {
            $columns = $fields;
        } else {
            $columns = static::getTableColumns();
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $columns) || is_null($value)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Get Model Columns
     * @return string
     */
    public static function getTableColumns()
    {
        return Schema::getColumnListing(static::getTableName());
    }

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function store($data)
    {
        $model = new static;
        $model->attributes = self::getFilter($data);
        $model->save();
        return $model->id;
    }

    /**
     * store
     * @param array $data
     * @return bool
     */
    public static function edit($id, $data)
    {
        $model = static::find($id);
        $model->attributes = self::getFilter($data);
        $model->save();
        return $model->id;
    }

    /**
     * @param mixed $condition  查询参数
     * @param $func
     * @param string $tag  cache标签
     * @param int $lifeTime 缓存生命周期
     * @return array
     */
    public static function setCacheData($func, $condition, $tag, $lifeTime = 60 * 10)
    {
        $key = self::genCacheKey($func, $condition, $tag);
        $cache = Cache::tags($tag);
        if ($cache->has($key)) {
           $data = $cache->get($key, []);
        } else {
            $data = call_user_func_array("$func",$condition);
            if (!empty($data)) {
                $cache->put($key, $data, $lifeTime);
            }
        }
        return $data;
    }

    /**
     * 清除DB缓存
     * @param $tag
     * @param $key
     * @return array
     */
    public static function clearCache($tag = '', $key = '')
    {
        if ($tag) {
            if ($key) {
                Cache::tags($tag)->forget($key);
            } else {
                Cache::tags($tag)->flush();
            }
        }
        return true;
    }

    protected static function genCacheKey($func, $condition, $tag)
    {
        return 'broadcast_' . md5($func . json_encode($condition) . $tag);
    }

}
