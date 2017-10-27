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
            '/auth/signup', 'UserController@store'
        );

        $app->post(
            '/auth/signin', 'AuthController@signin'
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

        // Developers
        $app->group(
            ['middleware' => 'jwt.auth'], function () use ($app) {
                $app->put(
                    '/users/{id}', 'UserController@update'
                );

                $app->patch(
                    '/users/{id}', 'UserController@update'
                );
            }
        );

        $app->group(
            [
                'middleware' => 'jwt.auth'
            ], function () use ($app) {

                // Recipes
                $app->get('/recipes', 'RecipeController@index')
                    ->post('/recipes', 'RecipeController@store');

                $app->put('/recipes/{recipeId}', 'RecipeController@update')
                    ->patch('/recipes/{recipeId}', 'RecipeController@update');

                $app->delete('/recipes/{recipeId}', 'RecipeController@delete');

                // Cookbooks
                $app->get('/cookbooks', 'CookbookController@index')
                    ->post('/cookbooks', 'CookbookController@store');

                $app->put('/cookbooks/{cookbookId}', 'CookbookController@update')
                    ->patch('/cookbooks/{cookbookId}', 'CookbookController@update');

                $app->delete('/cookbooks/{cookbookId}', 'CookbookController@delete');
            }
        );
    }
);
