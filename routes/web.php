<?php

/**
 * PHP version 8.0.0
 * @author   Florence Okosun <okosununzflorence@gmail.com>
 */

use Illuminate\Http\Response;

$router->get('/', function () {
	return response([
			'path' => '/',
			'api-version' => 'v1.0',
			'releases' => [
				'latest' => null,
			],
			'contribute' => 'mailto:developer@cookbookshq.com'
		], Response::HTTP_OK);
	}
);

$router->get('api/v1/create-auth-client', function() {
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

$router->group([
	'prefix' => 'api/v1',
	'middleware' => [
		'auth-guard',
		'throttle'
	]], function () use ($router) {

		/*
		|--------------------------------------------------------------------------
		| Search
		|--------------------------------------------------------------------------
		*/
        $router->get(
        	'/search', 'SearchController@fetch'
		);

		$router->post(
			'/keywords', 'SearchController@writeToCsv'
		);

		/*
		|--------------------------------------------------------------------------
		| Static content - user policies, terms and conditions
		|--------------------------------------------------------------------------
		*/
		$router->get(
			'/policies', 'StaticContentController@get'
		);

		/*
		|--------------------------------------------------------------------------
		| Users Auth
		|--------------------------------------------------------------------------
		*/
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
		| Cookbooks
		|--------------------------------------------------------------------------
		*/

		$router->get('/cookbooks', 'CookbookController@index');
		$router->get('/cookbooks/{cookbookId}', 'CookbookController@show');

		/*
		|--------------------------------------------------------------------------
		| Recipes
		|--------------------------------------------------------------------------
		*/
		$router->get('/recipes', 'RecipeController@index');
		$router->get('/recipes/{recipeId}', 'RecipeController@show');
		$router->post('add-clap', 'RecipeController@addClap');

		/*
		|--------------------------------------------------------------------------
		| Subscriptions
		|--------------------------------------------------------------------------
		*/
		$router->post('subscriptions', 'SubscriptionController@store');

		/*
		|--------------------------------------------------------------------------
		| Categories and Definitions
		|--------------------------------------------------------------------------
		*/

		$router->get('/categories', 'CategoryController@index'); //get all categories
		$router->get('/definitions', 'DefinitionsController@index'); //get all definitions

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
                $router->put(
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

				$router->delete('/recipes/{recipeId}', 'RecipeController@delete');

				/*
				|--------------------------------------------------------------------------
				| Cookbooks Routes
				|--------------------------------------------------------------------------
				*/

				$router->post('/cookbooks', 'CookbookController@store'); //create new cookbook
				$router->put('/cookbooks/{cookbookId}', 'CookbookController@update'); //update one cookbook
				$router->delete('/cookbooks/{cookbookId}', 'CookbookController@delete'); //delete one cookbook
            }
        );
    }
);
