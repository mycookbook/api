<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CookbookController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'v1'], function () {

    /*
    |--------------------------------------------------------------------------
    | Auth group
    |--------------------------------------------------------------------------
    |
    */
    Route::prefix('/auth')->group(function() {

        Route::post('/register', [UserController::class, 'store']);

        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/logout', [AuthController::class, 'logout']);

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
    });

    /*
    |--------------------------------------------------------------------------
    | Users group
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['prefix' => '/users'], function () {

        Route::get('/', [UserController::class, 'index']);

        Route::get('/{username}', [UserController::class, 'show']);

        Route::post('/{username}/edit', [UserController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Recipes group
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['prefix' => '/recipes'], function () {
        Route::get('/', [RecipeController::class, 'index']);
        Route::get('/{id}', [RecipeController::class, 'show']);

        Route::post('/', [RecipeController::class, 'store']);
        Route::post('/{id}/edit', [RecipeController::class, 'update']);
        Route::post('/{id}/destroy', [RecipeController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Cookbooks group
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['prefix' => '/cookbooks'], function () {
        Route::get('/', [CookbookController::class, 'index']);
        Route::get('//{id}', [CookbookController::class, 'show']);

        Route::post('/', [CookbookController::class, 'store']);
        Route::post('/{id}/edit', [CookbookController::class, 'update']);
        Route::post('/{id}/destroy', [CookbookController::class, 'destroy']);
    });

    Route::get('/tiktok', 'AuthController@tikTokHandleCallback');

    Route::get('/definitions', 'DefinitionsController@index');

    Route::get('/policies', 'StaticContentController@get');

    Route::get('/search', 'SearchController@fetch');

    Route::post('/keywords', 'SearchController@writeToCsv');

    Route::get('/my/recipes', [RecipeController::class, 'myRecipes']);

    Route::get('/my/cookbooks', [CookbookController::class, 'myCookbooks']);

    Route::get('verify-email/{token}', 'UserController@verifyEmail');

    Route::get('resend-email-verification-link/{token}', 'UserController@resend');

    Route::get('/stats/', 'StatsController@index');

    Route::post('subscriptions', 'SubscriptionController@store');

    Route::get('/categories', 'CategoryController@index');

    Route::post('/add-clap', 'RecipeController@addClap');
});
