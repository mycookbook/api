<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\UserRepository;

/**
 * Class UserController
 */
class UserController extends Controller
{
    protected $user;

    /**
     * @param UserRepository $user Userrepository
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }
    /**
     * Get all users from the database
     *
     * @return int
     */
    public function index()
    {
        return $this->user->index();
    }

    /**
     * Create new user resource
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function store(Request $request)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required|min:5'
            ]
        );

        return $this->user->store($request);
    }

    /**
     * Get one user
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->user->show($id);
    }

    /**
     * Implement a full/partial update
     *
     * @param Request $request request
     * @param int     $userId  userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $userId)
    {
        return $this->user->update($request, $userId);
    }
}
