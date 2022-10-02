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

Route::get('/', function () {
    return 'Cookbooks api v1';
});

Route::match(['POST', 'GET'],'/callback/tiktok', [
    'uses' => 'AuthController@tikTokHandleCallback',
    'provider' => 'tiktok',
]);

//twitter
Route::get('/twitter', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'twitter',
]);

//pinterest
Route::get('/pinterest', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'pinterest',
]);

//instagram
Route::get('/instagram', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'instagram',
]);
