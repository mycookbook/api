<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Services\AuthService;
use App\Http\Controllers\Requests\Auth\SignInRequest;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
	/**
	 * @param AuthService $service
	 */
	public function __construct(AuthService $service)
	{
		$this->service = $service;
	}

	/**
	 * Authenticate the user with AuthService
	 *
	 * @param SignInRequest $request
	 * @param JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login(SignInRequest $request, JWTAuth $jwt)
	{
		return $this->service->login($request->getParams(), $jwt);
	}
}
