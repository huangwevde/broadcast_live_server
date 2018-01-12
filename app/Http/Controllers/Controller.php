<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Http\Entity\ClientUser;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $client_id;

    public function __construct(Request $request)
    {
        if (env('APP_ENV') == 'dev') {
            $request->offsetSet('username', 'dxy_test');
            $request->offsetSet('password', 'dxy123123123');
        }
        $username = $request->get('username', '');
        $this->client_id = ClientUser::getClientId($username);
    }

    public function api($data = [], $msg = "success", $code = 0)
    {
        $api = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];

        return $api;
    }

    public function apiError($msg = "error", $code = -1)
    {
        $api = [
            'code' => $code,
            'msg' => $msg,
        ];

        return $api;
    }

}
