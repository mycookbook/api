<?php

namespace App\Services;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{
	/**
	 * Authenticate the user
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Tymon\JWTAuth\JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login(Request $request, JWTAuth $jwt)
	{
		$credentials = $request->only('email', 'password');

		if (! $token = $jwt->attempt($credentials) ) {
			return response()->json(
				[
					'Not found or Invalid Credentials.'
				], Response::HTTP_NOT_FOUND
			);
		}

		return response()->json(
			[
				'success' => true,
				'token' => $token,
				'username' => Auth::user()->getSlug(),
				'is_verified' => Auth::user()->is_verified
			], Response::HTTP_OK
		);
	}
}