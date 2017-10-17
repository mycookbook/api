<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * Get all users from the database
     *
     * @return int
     */
    public function index()
    {
        $users = User::with('Recipes', 'Cookbooks')->get();

        return response()->json(
            [
                'response' => [
                    'data' => $users->toArray()
                ]
            ], 200
        );
    }

    /**
     * Create new user resource
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function create(Request $request)
    {
        $response = [];

        $this->validate(
            $request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required|min:5'
            ]
        );

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $hashedPassword = (new BcryptHasher)->make($password);

        $user = new User(
            [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'following' => 0,
                'followers' => 0
            ]
        );

        if ($user->save()) {
            $response = response()->json(
                [
                    'response' => [
                        'created' => true,
                        'signin_uri' => '/api/v1/auth/signin'
                    ]
                ], 201
            );
        }

        return $response;
    }

    /**
     * Get one user
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($id)
    {
        $user = User::with('Recipes', 'Cookbooks')->find($id);

        if (! $user) {
            return response()->json(
                [
                    'error' => 'Record not found.'
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

    /**
     * Update user
     *
     * @param int $id unique identification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $user = $this->find($id);

        return $user;
    }
}
