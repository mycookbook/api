<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/**
 * Welcome and API documentation page
 */
$app->get(
    '/api/v1', function () use ($app) {
        return 'Cookbook API v1.0';
    }
);

$app->post(
    '/api/v1/signup', 'AuthController@create'
);

$app->post(
    '/api/v1/signin', 'AuthController@signin'
);

$app->put(
    '/api/v1/users/{id}', 'UserController@update'
);

$app->patch(
    '/api/v1/users/{id}', 'UserController@update'
);

$app->group(
    ['middleware' => 'throttle'], function () use ($app) {
        $app->get(
            '/api/v1/users/', 'UserController@getAllUsers'
        );

        $app->get(
            '/api/v1/users/{id}', 'UserController@getUser'
        );
    }
);

$app->post('/api/v1/users/{id}/cookbook', 'UserController@createCookbook');

$app->post(
    '/api/v1/users/{userId}/cookbook/{cookbookId}/recipe',
    'UserController@createRecipe'
);
