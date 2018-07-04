<?php

/**
 * Welcome API documentation page
 *
 * PHP version  7.0.12
 *
 * @category CookbookAPI
 * @package  CookbookAPi_V1
 * @author   Florence Okosun <okosununzflorence@gmail.com>
 * @license  MIT <http://somelink.com>
 * @link     Somelink <http://somelink.com>
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
            '/users/{username}', 'UserController@show'
        );

        $app->get(
            '/stats/', 'StatsController@index'
        );

        // Developers
        $app->group(
            ['middleware' => 'jwt.auth'], function () use ($app) {
                $app->put(
                    '/users/{username}', 'UserController@update'
                );

                $app->patch(
                    '/users/{username}', 'UserController@update'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Recipes Routes
        |--------------------------------------------------------------------------
        */

        $app->get('/recipes', 'RecipeController@index')
            ->post('/recipes', 'RecipeController@store');

        $app->put('/recipes/{recipeId}', 'RecipeController@update')
            ->patch('/recipes/{recipeId}', 'RecipeController@update')
            ->get('/recipes/{recipeId}', 'RecipeController@find');

        $app->delete('/recipes/{recipeId}', 'RecipeController@delete');

        /*
        |--------------------------------------------------------------------------
        | Cookbooks Routes
        |--------------------------------------------------------------------------
        */

        $app->get('/cookbooks', 'CookbookController@index'); //get all cookbooks

        $app->post('/cookbooks', 'CookbookController@store'); //create new cookbook

        $app->get(
            '/cookbooks/{cookbookId}', 'CookbookController@find'
        )  //get one cookbook
            ->put(
                '/cookbooks/{cookbookId}', 'CookbookController@update'
            ); //update one cookbook

        $app->delete('/cookbooks/{cookbookId}', 'CookbookController@delete'); //delete one cookbook

    }
);
