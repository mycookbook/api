<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
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
	public function login(Request $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
	{
		$credentials = $request->only('email', 'password');
		$user = User::where("email", $request->get("email"))->get()->first();
		Log::info('user', [$user]);

		if (!is_null($user)) {
			if (is_null($user->isVerified())) {
				return response()->json([
					'message' => 'not verified'
				], Response::HTTP_NOT_ACCEPTABLE);
			}
		}

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

    /**
     * @param Request $request
     * @param JWTAuth $jwt
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialAuth(Request $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'access_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
            ], ResponseAlias::HTTP_OK
        );
    }
}