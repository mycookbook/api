<?php

use Illuminate\Http\Response;

$router->get('/', function () {
    return 'Cookbooks api v1';
});

$router->get('/api/v1/users/{id}/verify', function ($id) {
    $user = \App\User::where(["id" => $id])->orWhere(["email" => $id])->get()->first();

    $user->update([
        'email_verified' => \Carbon\Carbon::now()
    ]);

    return $user;

});

$router->get('api/v1/create-auth-client', function () {
    $api_key = \Illuminate\Support\Facades\Crypt::encryptString('Hello DevDojo');

    $client = new \App\AuthorizedClient([
        'api_key' => $api_key,
        'client_secret' => 'Hello DevDojo',
        'passphrase' => 'potatoes'
    ]);

    if ($client->save()) {
        return $client;
    }

    return null;
});

$router->group(['prefix' => 'api/v1',], function () use ($router) {
    $router->post(
        '/auth/register', 'UserController@store'
    );

    $router->post(
        '/auth/login', 'AuthController@login'
    );

    $router->get(
        '/tiktok', 'AuthController@tikTokHandleCallback'
    );

    $router->get('/definitions', 'DefinitionsController@index');
    $router->get(
        '/policies', 'StaticContentController@get'
    );

    $router->get('/cookbooks', 'CookbookController@index');
    $router->get('/cookbooks/{id}', 'CookbookController@show');

    $router->get('/recipes', 'RecipeController@index');
    $router->get('/recipes/{recipeId}', 'RecipeController@show');

    $router->get(
        '/users/', 'UserController@index'
    );

    $router->get(
        '/users/{username}', 'UserController@show'
    );

    $router->get(
        '/search', 'SearchController@fetch'
    );

    $router->post(
        '/keywords', 'SearchController@writeToCsv'
    );

    $router->group([
        'middleware' => [
            'auth-guard',
            'throttle'
        ]], function () use ($router) {

        $router->get('flags', function () {
            return response()->json([
                "data" => \App\Flag::all()
            ]);
        });

        $router->get('categories', function () {
            return response()->json([
                "data" => \App\Category::all()
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */
        $router->get(
            '/stats/', 'StatsController@index'
        );

        /*
        |--------------------------------------------------------------------------
        | Recipes
        |--------------------------------------------------------------------------
        */
        $router->get('/my/recipes', 'RecipeController@myRecipes');

        /**
         * Email verification
         */
        $router->get('verify-email/{token}', 'UserController@verifyEmail');
        $router->get('resend-email-verification-link/{token}', 'UserController@resend');

        /*
        |--------------------------------------------------------------------------
        | PROTECTED ROUTES
        |--------------------------------------------------------------------------
        */
        $router->group(
            [
                'middleware' => [
                    'jwt.auth'
                ]
            ], function () use ($router) {
            $router->post(
                '/users/{username}', 'UserController@update'
            );

            $router->patch(
                '/users/{username}', 'UserController@update'
            );

            /*
            |--------------------------------------------------------------------------
            | Recipes Routes
            |--------------------------------------------------------------------------
            */
            $router->post('/recipes', 'RecipeController@store');

            $router->put('/recipes/{recipeId}', 'RecipeController@update')
                ->patch('/recipes/{recipeId}', 'RecipeController@update');

//            $router->delete('/recipes/{recipeId}', 'RecipeController@delete');

            /*
            |--------------------------------------------------------------------------
            | Cookbooks Routes
            |--------------------------------------------------------------------------
            */
            $router->get('/my/cookbooks', 'CookbookController@myCookbooks');

            $router->post('/cookbooks', 'CookbookController@store');
            $router->put('/cookbooks/{id}', 'CookbookController@update');
//            $router->delete('/cookbooks/{cookbookId}', 'CookbookController@delete');
        });
    });

    /*
     * |--------------------------------------------------------------------------
     * | Subscriptions
     * |--------------------------------------------------------------------------
   */
    $router->post('subscriptions', 'SubscriptionController@store');

    $router->get('/categories', 'CategoryController@index');

    $router->post('/add-clap', 'RecipeController@addClap');

    $router->get('/auth/tiktok', function() {

    });
});
