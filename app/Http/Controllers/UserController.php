<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Controllers\Requests\User\StoreRequest;
use App\Http\Controllers\Requests\User\UpdateRequest;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all users from the database
     */
    public function index()
    {
        return $this->service->index();
    }

	/**
	 * Create new user
	 *
	 * @param \App\Http\Controllers\Requests\User\StoreRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function store(StoreRequest $request)
    {
        return $this->service->store($request->getParams());
    }

	/**
	 * Get one user
	 *
	 * @param int $username username
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function show($username)
    {
        return $this->service->show($username);
    }

	/**
	 * Implement a full/partial update
	 *
	 * @param \App\Http\Controllers\Requests\User\UpdateRequest $request
	 * @param string $userId userName
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
	public function update(UpdateRequest $request, $userId)
	{
		return $this->service->update($request->getParams(), $userId);
	}
}
