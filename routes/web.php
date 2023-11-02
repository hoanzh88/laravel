<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'localization'], function () {
	Route::get('/', function () {
		return view('welcome');
	});
	Route::get('/hello-world', 'DevController@index')->name('dev.index');

	Route::prefix('product')->group(function () {
		Route::get('/', '\App\Http\Controllers\ProductController@index');
		
		Route::get('/create', '\App\Http\Controllers\ProductController@create');
		Route::post('/', '\App\Http\Controllers\ProductController@store');
		
		Route::get('/{product_id}/edit', '\App\Http\Controllers\ProductController@edit')->name('product.edit');	
		Route::put('/{product_id}', '\App\Http\Controllers\ProductController@update');
		
		Route::get('/{product_id}/delete', '\App\Http\Controllers\ProductController@destroy')->name('product.delete');	
	});

	Route::prefix('users')->group(function () {
		Route::get('/login', '\App\Http\Controllers\UsersController@getLogin')->name('users.login');
		Route::post('/login', '\App\Http\Controllers\UsersController@checkLogin')->name('users.login');
		Route::get('/logout', '\App\Http\Controllers\UsersController@getLogout')->name('users.logout');
		Route::get('/', '\App\Http\Controllers\UsersController@index')->middleware('checkuserslogin')->name('users.index');
	});
});
Route::get('change-language/{language}', 'LanguageController@changeLanguage')->name('change_language');
