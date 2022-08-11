<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\Cookbook\StoreRequest;
use App\Services\CookbookService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class UserController
 */
class CookbookController extends Controller
{
    /**
     * @param  Request  $request
     * @param  \App\Services\CookbookService  $service
     */
    public function __construct(Request $request, CookbookService $service)
    {
        $this->middleware('jwt.auth', ['except' => [
            'index',
            'show',
        ]]);

        $this->service = $service;

        parent::__construct($request);
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCookbooks(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->service->index($request->get('user_id'));
    }

    /**
     * Create cookbook for user
     *
     * @param  \App\Http\Controllers\Requests\Cookbook\StoreRequest  $request
     * @param  \Tymon\JWTAuth\JWTAuth  $jwt
     * @return \Illuminate\Http\JsonResponse
     *
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
     * @param  int  $id
     * @param  Request  $request req
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function update(int $id, Request $request)
    {
        return $this->service->update($request, $id);
    }

    /**
     * Delete a cookbook
     *
     * @param  int  $cookbookId cookbookId
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function delete($cookbookId)
    {
        return $this->service->delete($cookbookId);
    }

    /**
     * Find resource
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function show($id)
    {
        return $this->service->show($id);
    }
}
