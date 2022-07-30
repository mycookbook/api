<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
	public function login(SignInRequest $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
	{
		return $this->service->login($request->getParams(), $jwt);
	}

    /**
     * @param Request $request
     * @param JWTAuth $jwt
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialAuth(Request $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
    {
        return $this->service->socialAuth($request, $jwt);
    }
}
