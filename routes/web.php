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
    '/api', function () use ($app) {
        return 'Cookbook API v1.0';
    }
);

$app->group(
    ['middleware' => 'throttle'], function () use ($app) {
        $app->get(
            '/api/users/', 'UserController@getAllUsers'
        );

        $app->get(
            '/api/users/{id}', 'UserController@getUser'
        );
    }
);
