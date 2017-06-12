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

use App\User;

/**
 * Landing page for API documentation
 */
$app->get(
    '/api', function () use ($app) {
        return 'Cookbook API v1.0';
    }
);

/**
 * Get all users in the database
 */
$app->get('/api/users', 'UserController@getAllUsers');

/**
 * Get one user from the database
 */
$app->get(
    '/api/user/{id}', 'UserController@getUser'
);
