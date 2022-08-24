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

//tiktok
Route::get('/tiktok', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'tiktok',
]);

Route::get('/callback/tiktok', [
    'uses' => 'AuthController@socialAuthCallbackHandler',
    'provider' => 'tiktok',
]);

//twitter
Route::get('/twitter', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'twitter',
]);

Route::get('/callback/twitter', [
    'uses' => 'AuthController@socialAuthCallbackHandler',
    'provider' => 'twitter',
]);

//pinterest
Route::get('/pinterest', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'pinterest',
]);

Route::get('/callback/pinterest', [
    'uses' => 'AuthController@socialAuthCallbackHandler',
    'provider' => 'pinterest',
]);

//instagram
Route::get('/instagram', [
    'uses' => 'AuthController@socialAuth',
    'provider' => 'instagram',
]);

Route::get('/callback/instagram', [
    'uses' => 'AuthController@socialAuthCallbackHandler',
    'provider' => 'instagram',
]);

Route::get('/webhooks/tiktok', function() {
    return response()->json([
        'message' => 'payload recieved with thanks'
    ]);
});