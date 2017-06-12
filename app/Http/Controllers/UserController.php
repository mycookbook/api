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
        $users = User::all();

        if (count($users) > 0) {
            return response()->json(
                [
                    'data' => $users,
                    'message' => 'success',
                    'status' => '200',
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => [],
                    'message' => 'No Data',
                    'status' => '404'
                ]
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
        $user = User::with('Recipes', 'Cookbooks')->get();

        if ($user) {
            return response()->json(
                [
                    'data' => $user,
                    'message' => 'success',
                    'status' => '200',
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => [],
                    'message' => 'User not found',
                    'status' => '404'
                ]
            );
        }
    }
}
