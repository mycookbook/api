<?php
/**
 * UserController
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

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
     * Create new user
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function create(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        return response()->json(
            [
                'response' => [
                    'success' => true,
                    'data' => [$name, $email, $password]
                ]
            ], 200
        );
    }

    /**
     * Signin
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function signin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        return response()->json(
            [
                'response' => [
                    'success' => true,
                    'data' => [$email, $password]
                ]
            ], 200
        );
    }

    /**
     * Get all users fromt he database
     *
     * @return int
     */
    public function getAllUsers()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        if (count($users) < 1) {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => null
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'success' => true,
                    'data' => $users->toArray()
                ]
            ], 200
        );
    }

    /**
     * Get one user
     *
     * @param int $id id
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
                        'success' => false,
                        'data' => null
                    ]
                ], 404
            );
        }

        return response()->json(
            [
                'response' => [
                    'success' => false,
                    'data' => $user->toArray()
                ]
            ], 200
        );
    }
}
