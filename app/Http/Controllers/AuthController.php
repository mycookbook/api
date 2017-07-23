<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

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

        $user = new User(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        if ($user->save()) {
            return response()->json(
                [
                    'response' => ['created' => true]
                ], 201
            );
        } else {
            return response()->json(
                [
                    'response' => [
                        'success' => false,
                        'data' => 'Email exists already'
                    ]
                ], 401
            );
        }
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
}
