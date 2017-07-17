<?php
/**
 * UserController
 */

namespace App\Http\Controllers;

use App\User;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Get all users fromt he database
     *
     * @return int
     */
    public function getAllUsers()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        //dd(count($users));

        if (count($users) < 1) {
            return response()->json(
                [
                    'response' => [
                        'status' => 'error',
                        'data' => null,
                        'message' => 'No data!',
                        'code' => 404,
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'status' => 'success',
                    'data' => $users->toArray(),
                    'message' => 'success',
                    'code' => 200,
                ]
            ], 200
        );
    }

    /**
     * Get one user
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        $user = User::with('Recipes', 'Cookbooks')->find($id);

        if (! $user) {
            return response()->json(
                [
                    'response' => [
                        'status' => 'error',
                        'data' => null,
                        'message' => 'User not found!',
                        'code' => 404,
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'status' => 'success',
                    'data' => $user->toArray(),
                    'message' => 'user found.',
                    'code' => 200,
                ]
            ], 200
        );
    }
}
