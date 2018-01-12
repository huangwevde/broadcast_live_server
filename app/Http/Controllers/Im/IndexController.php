<?php

namespace App\Http\Controllers\Im;

use App\Http\Controllers\Controller;
use App\Http\Utils\TLSSigAPI;
use App\Http\Utils\Tools;
use Illuminate\Http\Request;
use Log;

class IndexController extends Controller
{

    /**
    * 生产usersig
    * @param string $identifier 用户名
    * @return string
    */
    public function getSig(Request $request) 
    {
        $data = $request->all();
        if (!Tools::custom_validate_array($data, ['identifier'])) {
            return $this->apiError('参数错误');
        }
        $tlsApi = new TLSSigAPI();
        $sig = $tlsApi->genSig($data['identifier']);
        return $this->api($sig);
    }

}
