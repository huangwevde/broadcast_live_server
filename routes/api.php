<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(
    [
        'middleware' => ['auth.api.user'],
        'prefix' => '',
    ],
    function () {
        Route::any('test', 'Integration\IndexController@index');

        Route::any('config/info', 'Integration\IndexController@index');

        // 文档上传
        Route::any('file/upload', 'Integration\FileController@upload');
        Route::any('file/docImages/{id}', 'Integration\FileController@docImages');

        // 直播管理
        Route::any('live/index', 'Integration\LiveController@index');
        Route::post('live/create', 'Integration\LiveController@store');
        Route::any('live/show/{id}', 'Integration\LiveController@show');
        Route::any('live/edit/{id}', 'Integration\LiveController@edit');
        Route::any('live/destroy/{id}', 'Integration\LiveController@destroy');
        Route::any('live/push/{id}', 'Integration\LiveController@livePush');
        Route::any('live/start/{id}', 'Integration\LiveController@startLive');
        Route::get('live/play/{id}', 'Integration\LiveController@getPlayUrl');
        // Route::resource('live', 'Integration\LiveController', [
        //     'names' => [
        //         'index' => 'live_index',
        //         'show' => 'live_show',
        //         'store' => 'live_store',
        //         'update' => 'live_update',
        //         'destroy' => 'live_destroy',
        //     ]
        // ]);

        Route::any('mix', 'Integration\MixStreamController@index');

        // 聊天室
        Route::any('im/getSig', 'Im\IndexController@getSig');
        Route::any('im/group/list', 'Im\GroupController@index');
        Route::any('im/group/create', 'Im\GroupController@createGroup');
        Route::any('im/group/add', 'Im\GroupController@addGroup');
        Route::any('im/group/del', 'Im\GroupController@delGroup');
        Route::any('im/group/getJoined', 'Im\GroupController@getJoinedGroup');
    }
);

Route::group(
    [
        'middleware' => ['auth.api.tencent'],
        'prefix' => 'live',
    ],
    function () {
        Route::any('callback', 'Integration\LiveCallbackController@index');
    }
);

Route::group(
    [
        'prefix' => 'im',
    ],
    function () {
        Route::any('callback', 'Im\ImCallbackController@index');
    }
);

Route::group(
    [
        'prefix' => 'qiniu',
    ],
    function () {
        Route::any('notify/{fid}/{type}', 'Integration\FileController@notify');
    }
);