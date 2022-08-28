<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CookbookController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'v1'], function () {

    //    Route::get('/tiktok', 'AuthController@tikTokHandleCallback');

    Route::get('/callback/twitter', [
        'uses' => 'AuthController@socialAuthCallbackHandler',
        'provider' => 'twitter',
    ]);

    Route::get('/callback/pinterest', [
        'uses' => 'AuthController@socialAuthCallbackHandler',
        'provider' => 'pinterest',
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

    Route::get('/definitions', 'DefinitionsController@index');

    Route::get('/policies', 'StaticContentController@get');

    Route::get('/search', [SearchController::class, 'getSearchResults']);

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
