<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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

        try {
            if (! $token = $jwt->attempt($credentials) ) {
                return response()->json(
                    [
                        'Not found or Invalid Credentials.'
                    ], 404
                );
            }
        } catch (TokenExpiredException $e) {
            return response()->json(
                [
                    'token_expired'
                ], 500
            );
        } catch (TokenInvalidException $e) {
            return response()->json(
                [
                    'token_invalid'
                ], 500
            );
        } catch (JWTException $e) {
            return response()->json(
                [
                    'token_absent' => $e->getMessage()
                ], 500
            );
        }

        return response()->json(
            [
                'success' => true,
                'token' => $token
            ], 200
        );
    }
}
