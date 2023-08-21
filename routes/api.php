<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CookbookController;
use App\Http\Controllers\DefinitionsController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StaticContentController;
use App\Http\Controllers\UserController;
use App\Models\Flag;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::get('/ping', function () {
        return 'Cookbooks api v1';
    });

    Route::get('/callback/tiktok', [
        'uses' => 'AuthController@tikTokHandleCallback',
        'provider' => 'twitter',
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

        Route::post('/validate', [AuthController::class, 'validateToken']);

        Route::post('/magiclink', [AuthController::class, 'loginViaMagicLink']);
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

        Route::group(['prefix' => '/tiktok'], function () {
            Route::get('/videos', [UserController::class, 'listVideos']);
        });
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
        Route::get('/{id}', [CookbookController::class, 'show']);

        Route::post('/', [CookbookController::class, 'store']);
        Route::post('/{id}/edit', [CookbookController::class, 'update']);
        Route::post('/{id}/destroy', [CookbookController::class, 'destroy']);
    });

    Route::get('/definitions', [DefinitionsController::class, 'index']);

    Route::get('/policies', [StaticContentController::class, 'get']);

    Route::get('/flags', array(Flag::class, 'getAll'));

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

    Route::post('/comments', [CommentController::class, 'addComment']);

    Route::post('/comments/destroy', [CommentController::class, 'destroyComment']);

    Route::post('/follow', [UserController::class, 'followUser']);

    Route::get('/who-to-follow', [UserController::class, 'getWhoToFollow']);

    Route::post('/feedback', [UserController::class, 'addFeedback']);

    Route::post('/report-recipe', [RecipeController::class, 'report']);

    Route::group(['prefix' => '/otp'], function () {
        Route::post('/generate', [UserController::class, 'generateOtp']);
        Route::post('/validate', [UserController::class, 'validateOtp']);
    });
})->middleware(['api']);
