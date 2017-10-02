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
        )->post(
            '/', function () {
                return 'tryna to post';
            }
        );

        $app->post(
            '/signup', 'AuthController@create'
        );

        $app->post(
            '/signin', 'AuthController@signin'
        );

        $app->put(
            '/user/{id}', 'UserController@update'
        );

        $app->patch(
            '/user/{id}', 'UserController@update'
        );

        // Developers
        $app->group(
            ['middleware' => 'throttle:30'], function () use ($app) {
                $app->get(
                    '/users/', 'UserController@index'
                );

                $app->get(
                    '/user/{id}', 'UserController@find'
                );

                $app->get(
                    '/stats/', 'StatsController@index'
                );
            }
        );

        $app->group(
            ['middleware' => 'jwt.auth'], function () use ($app) {
                // Recipes
                $app->get('/recipes', 'RecipeController@index');
                $app->post(
                    '/cookbook/{cookbookId}/recipe',
                    'RecipeController@store'
                );

                // Cookbooks
                $app->post('/users/{id}/cookbook', 'CookbookController@store');
            }
        );
    }
);
