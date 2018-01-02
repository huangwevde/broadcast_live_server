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
        Route::any('mix', 'Integration\MixStreamController@index');

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