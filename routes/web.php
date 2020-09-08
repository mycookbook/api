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

$router->group(
    ['prefix' => 'api/v1'], function () use ($router) {
        $router->get(
            '/', function () {
                return 'Cookbook API v1.0';
            }
        );

        $router->get(
        	'/search', 'SearchController@fetch'
		);

        $router->post(
            '/auth/register', 'UserController@store'
        );

        $router->post(
            '/auth/login', 'AuthController@login'
        );

        $router->get(
            '/users/', 'UserController@index'
        );

        $router->get(
            '/users/{username}', 'UserController@show'
        );

        $router->get(
            '/stats/', 'StatsController@index'
        );

        // Developers
        $router->group(
            ['middleware' => 'jwt.auth'], function () use ($router) {
                $router->put(
                    '/users/{username}', 'UserController@update'
                );

                $router->patch(
                    '/users/{username}', 'UserController@update'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Recipes Routes
        |--------------------------------------------------------------------------
        */

        $router->get('/recipes', 'RecipeController@index')
            ->post('/recipes', 'RecipeController@store');

        $router->put('/recipes/{recipeId}', 'RecipeController@update')
            ->patch('/recipes/{recipeId}', 'RecipeController@update')
            ->get('/recipes/{recipeId}', 'RecipeController@show');

        $router->delete('/recipes/{recipeId}', 'RecipeController@delete');

        /*
        |--------------------------------------------------------------------------
        | Cookbooks Routes
        |--------------------------------------------------------------------------
        */

        $router->get('/cookbooks', 'CookbookController@index'); //get all cookbooks
        $router->post('/cookbooks', 'CookbookController@store'); //create new cookbook
        $router->get('/cookbooks/{cookbookId}', 'CookbookController@show');
        $router->put('/cookbooks/{cookbookId}', 'CookbookController@update'); //update one cookbook
        $router->delete('/cookbooks/{cookbookId}', 'CookbookController@delete'); //delete one cookbook

		$router->post('subscriptions', 'SubscriptionController@store');


		/*
		|--------------------------------------------------------------------------
		| Categories Routes
		|--------------------------------------------------------------------------
		*/

		$router->get('/categories', 'CategoryController@index'); //get all categories
		$router->get('/definitions', 'DefinitionsController@index'); //get all definitions

		/*
		* recipes claps
		*/

		$router->post('add-clap', 'RecipeController@addClap'); //increment recipe clap

		/**
		 * Email verification
		 */
		$router->get('verify-email/{token}', 'UserController@verifyEmail');
    }
);
