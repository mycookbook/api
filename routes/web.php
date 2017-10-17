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
            '/auth/signup', 'UserController@store'
        );

        $app->post(
            '/auth/signin', 'AuthController@signin'
        );

        // Developers
        $app->group(
            ['middleware' => 'throttle:30'], function () use ($app) {
                $app->put(
                    '/users/{id}', 'UserController@update'
                );

                $app->patch(
                    '/users/{id}', 'UserController@update'
                );

                $app->get(
                    '/users/', 'UserController@index'
                );

                $app->get(
                    '/users/{id}', 'UserController@show'
                );

                $app->get(
                    '/stats/', 'StatsController@index'
                );
            }
        );

        $app->group(
            ['middleware' => 'jwt.auth'], function () use ($app) {
                // Recipes
                $app->get('/recipes', 'RecipeController@index')
                    ->post('/recipes', 'RecipeController@store');
                $app->put('/recipes/{recipeId}', 'RecipeController@update');
                $app->delete('/recipes/{recipeId}', 'RecipeController@delete');

                // Cookbooks
                $app->post('/cookbooks', 'CookbookController@store')
                    ->get('/cookbooks', 'CookbookController@index');

                $app->get(
                    '/cookbooks/{cookbookId}/users',
                    'CookbookController@getUsers'
                );

                $app->get(
                    '/cookbooks/{cookbookId}/recipes',
                    'CookbookController@getRecipes'
                );

                $app->put('/cookbooks/{cookbookId}', 'CookbookController@update');

                $app->delete('/cookbooks/{cookbookId}', 'CookbookController@delete');
            }
        );
    }
);
