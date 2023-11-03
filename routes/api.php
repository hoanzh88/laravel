<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'Auth', 'middleware' => 'cors'], function () {
    Route::post('login', ['as' => 'login', 'uses' => 'AdminLoginController@login']);
});

Route::group(['prefix' => 'v1', 'namespace' => 'Auth', 'middleware' => 'cors'], function () {
    Route::post('logout', ['as' => 'logout', 'uses' => 'AdminLoginController@logout']);
});

Route::group(['prefix' => 'v1', 'namespace' => 'Admin'], function () {
    Route::group(['prefix' => 'route','middleware' => 'admin.api'], function () {
        Route::get('admin/list',['as'=>'list.route','uses'=>'AdminAccountController@list']);
    });
});