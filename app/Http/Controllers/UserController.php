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

        if (count($users) > 0) {
            return response()->json(
                [
                    'response' => [
                        'data' => $users,
                        'code' => 200,
                    ]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'No data!',
                        'code' => 404,
                    ]
                ], 404
            );
        }
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

        if ($user) {
            return response()->json(
                [
                    'response' => [
                        'data' => $user,
                        'code' => 200,
                    ]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'errors' => [
                        'message' => 'User does not exist!',
                        'code' => 404,
                    ]
                ], 404
            );
        }
    }
}
