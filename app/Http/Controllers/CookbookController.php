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
    public function index()
    {
        return $this->service->index();
    }

	/**
	 * Create cookbook for user
	 *
	 * @param \App\Http\Controllers\Requests\Cookbook\StoreRequest $request
	 * @param \Tymon\JWTAuth\JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 */
    public function store(StoreRequest $request, JWTAuth $jwt)
    {
		$jwt->parseToken()->authenticate();
    	return $this->service->store($request->getParams());
    }

	/**
	 * Update cookbook
	 *
	 * @param Request $request req
	 * @param int $cookbookId
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \App\Exceptions\CookbookModelNotFoundException
	 */
    public function update(Request $request, $cookbookId)
    {
        return $this->service->update($request, $cookbookId);
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
	 * @param int $id
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 * @throws \App\Exceptions\CookbookModelNotFoundException
	 */
    public function show($id)
    {
    	return $this->service->show($id);
    }
}
