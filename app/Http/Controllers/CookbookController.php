<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Services\CookbookService;
use App\Http\Controllers\Requests\Cookbook\StoreRequest;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class CookbookController extends Controller
{
    /**
	 * @param \App\Services\CookbookService $service
     */
    public function __construct(CookbookService $service)
    {
        $this->middleware('jwt.auth', ['except' => [
            'index',
            'show'
        ]]);

        $this->service = $service;
    }

    /**
     * Return all the cookbooks and associated recipes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
	{
        return $this->service->index();
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function myCookbooks(Request $request): \Illuminate\Http\JsonResponse
	{
		return $this->service->index($request->get("user_id"));
	}

	/**
	 * Create cookbook for user
	 *
	 * @param \App\Http\Controllers\Requests\Cookbook\StoreRequest $request
	 * @param \Tymon\JWTAuth\JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 * @throws \Exception
	 */
    public function store(StoreRequest $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
	{
    	$jwt->parseToken()->authenticate();
    	return $this->service->store($request->getParams());
    }

	/**
	 * Update cookbook
	 *
	 * @param int $id
	 *
	 * @param Request $request req
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \App\Exceptions\CookbookModelNotFoundException
	 */
    public function update(int $id, Request $request)
    {
        return $this->service->update($request, $id);
    }

	/**
	 * Delete a cookbook
	 *
	 * @param int $cookbookId cookbookId
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \App\Exceptions\CookbookModelNotFoundException
	 */
    public function delete($cookbookId)
    {
        return $this->service->delete($cookbookId);
    }

	/**
	 * Find resource
	 *
	 * @param mixed $cookbookId
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \App\Exceptions\CookbookModelNotFoundException
	 */
    public function show($cookbookId)
    {
    	return $this->service->show($cookbookId);
    }
}
