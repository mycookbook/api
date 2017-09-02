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

$app->group(
    ['prefix' => 'api/v1'], function () use ($app) {
        $app->get(
            '/', function () {
                return 'Cookbook API v1.0';
            }
        );

        $app->post(
            '/signup', 'AuthController@create'
        );

        $app->post(
            '/signin', 'AuthController@signin'
        );

        $app->put(
            '/users/{id}', 'UserController@update'
        );

        $app->patch(
            '/users/{id}', 'UserController@update'
        );

        $app->group(
            ['middleware' => 'throttle'], function () use ($app) {
                $app->get(
                    '/users/', 'UserController@index'
                );

                $app->get(
                    '/users/{id}', 'UserController@find'
                );
            }
        );

        $app->post('/users/{id}/cookbook', 'UserController@store');

        $app->post(
            '/user/cookbook/{cookbookId}/recipe',
            'RecipeController@store'
        );

        $app->group(
            ['middleware' => 'jwt.auth'], function () use ($app) {
                $app->get('/user/recipes', 'RecipeController@index');
            }
        );
    }
);

