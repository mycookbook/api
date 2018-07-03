<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * Signin
     *
     * @param Request $request form inputs
     * @param JWTAuth $jwt     jwt
     *
     * @return array|string
     */
    public function signin(Request $request, JWTAuth $jwt)
    {
        $this->validate(
            $request, [
                'email' => 'required',
                'password' => 'required'
            ]
        );

        $credentials = $request->only('email', 'password');

        if (! $token = $jwt->attempt($credentials) ) {
            return response()->json(
                [
                    'Not found or Invalid Credentials.'
                ], 404
            );
        }

        return response()->json(
            [
                'success' => true,
                'token' => $token,
                'username' => Auth::user()->name_slug
            ], 200
        );
    }
}
