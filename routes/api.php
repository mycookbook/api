<?php

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

Route::get('/v1/users/{id}/verify', function ($id) {
    $user = \App\User::where(['id' => $id])->orWhere(['email' => $id])->get()->first();

    $user->update([
        'email_verified' => \Carbon\Carbon::now(),
    ]);

    return $user;
});

Route::get('v1/create-auth-client', function () {
    $api_key = \Illuminate\Support\Facades\Crypt::encryptString('Hello DevDojo');

    $client = new \App\AuthorizedClient([
        'api_key' => $api_key,
        'client_secret' => 'Hello DevDojo',
        'passphrase' => 'potatoes',
    ]);

    if ($client->save()) {
        return $client;
    }

    return null;
});

Route::group(['prefix' => 'v1'], function () {

    Route::prefix('/auth')->group(function() {

        Route::post(
            '/register', 'UserController@store'
        );

        Route::post(
            '/login', 'AuthController@login'
        );

        Route::get(
            '/logout', 'AuthController@logout'
        );

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

    Route::get(
        '/tiktok', 'AuthController@tikTokHandleCallback'
    );

    Route::get('/definitions', 'DefinitionsController@index');
    Route::get(
        '/policies', 'StaticContentController@get'
    );

    Route::get('/cookbooks', 'CookbookController@index');
    Route::get('/cookbooks/{id}', 'CookbookController@show');

    Route::get('/recipes', 'RecipeController@index');
    Route::get('/recipes/{recipeId}', 'RecipeController@show');

    Route::get(
        '/users/', 'UserController@index'
    );

    Route::get(
        '/users/{username}', 'UserController@show'
    );

    Route::get(
        '/search', 'SearchController@fetch'
    );

    Route::post(
        '/keywords', 'SearchController@writeToCsv'
    );

    Route::group([
        'middleware' => [
            'auth-guard',
            'throttle',
        ], ], function () {
        Route::get('flags', function () {
            return response()->json([
                'data' => \App\Flag::all(),
            ]);
        });

        Route::get('categories', function () {
            return response()->json([
                'data' => \App\Category::all(),
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/stats/', 'StatsController@index'
        );

        /*
        |--------------------------------------------------------------------------
        | Recipes
        |--------------------------------------------------------------------------
        */
        Route::get('/my/recipes', 'RecipeController@myRecipes');

        /**
         * Email verification
         */
        Route::get('verify-email/{token}', 'UserController@verifyEmail');
        Route::get('resend-email-verification-link/{token}', 'UserController@resend');

        /*
        |--------------------------------------------------------------------------
        | PROTECTED ROUTES
        |--------------------------------------------------------------------------
        */
        Route::group(
            [
                'middleware' => [
                    'jwt.auth',
                ],
            ], function () {
            Route::post(
                '/users/{username}', 'UserController@update'
            );

            Route::patch(
                '/users/{username}', 'UserController@update'
            );

            /*
            |--------------------------------------------------------------------------
            | Recipes Routes
            |--------------------------------------------------------------------------
            */
            Route::post('/recipes', 'RecipeController@store');

            Route::put('/recipes/{recipeId}', 'RecipeController@update');
            Route::patch('/recipes/{recipeId}', 'RecipeController@update');

//            $router->delete('/recipes/{recipeId}', 'RecipeController@delete');

            /*
            |--------------------------------------------------------------------------
            | Cookbooks Routes
            |--------------------------------------------------------------------------
            */
            Route::get('/my/cookbooks', 'CookbookController@myCookbooks');

            Route::post('/cookbooks', 'CookbookController@store');
            Route::put('/cookbooks/{id}', 'CookbookController@update');
//            $router->delete('/cookbooks/{cookbookId}', 'CookbookController@delete');
        });
    });

    /*
     * |--------------------------------------------------------------------------
     * | Subscriptions
     * |--------------------------------------------------------------------------
   */
    Route::post('subscriptions', 'SubscriptionController@store');

    Route::get('/categories', 'CategoryController@index');

    Route::post('/add-clap', 'RecipeController@addClap');
});
