<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Initialise class
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
                    'response' => ['created' => true]
                ], 201
            );
        }

        return $response;
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
        $this->validate(
            $request, [
                'email' => 'required',
                'password' => 'required'
            ]
        );

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
}
