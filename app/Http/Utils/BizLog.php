<?php

namespace App\Http\Utils;

use Log;

class BizLog
{

    protected static $debug;

    /**
    * BizLog info
    */
    public static function info($msg)
    {
        if (env('BIZ_DEBUG', false)) {
            if (is_object($msg)) {
                $msg = (array)$msg;
            }
            Log::info($msg);
        }
    }

    /**
    * BizLog error
    */
    public static function error($msg)
    {
        if (env('BIZ_DEBUG', false)) {
            Log::error($msg);
        }
    }

}
