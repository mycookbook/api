<?php

namespace App\Http\Repositories;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

/**
 * Class UserRepository
 */
class UserRepository
{
    /**
     * Get all users from the database
     *
     * @return int
     */
    public function index()
    {
        $users = User::all();

        return response(
            [
                'data' => $users
            ]
        );
    }

    /**
     * Create new user resource
     *
     * @param Request $request form inputs
     *
     * @return array|string
     */
    public function store($request)
    {
        $user = new User(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => (new BcryptHasher)->make($request->password),
                'following' => 0,
                'followers' => 0
            ]
        );

        $data = $user->save();

        $statusCode = $user ? 201 : 422;

        return response()->json(
            [
                'response' => [
                    'created' => true,
                    'data' => self::userExist($user->id),
                    'status' => $data ? "success" : "error",
                ]
            ], $statusCode
        );
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
        return self::userExist($id);
    }

    /**
     * Implement a full/partial update
     *
     * @param Request $request request
     * @param int     $id      userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findorFail($id);
            $updated = $user->update($request->except(['email']));
            $statusCode = $updated ? 202 : 422;
            $status = "success";
        } catch(\Exception $e) {
            $updated = false;
            $statusCode = 404;
            $status = ['error' => $e->getMessage()];
        }

        return response(
            [
                "updated" => $updated,
                "status" => $status,
            ], $statusCode
        );
    }

    /**
     * Check if user exist by id
     *
     * @param int $id id
     *
     * @return bool|mixed|static
     */
    protected static function userExist($id)
    {
        try {
            $response = User::findOrFail($id);
        } catch(\Exception $e) {
            $response = response(
                [
                    'error' => $e->getMessage(),
                ], 404
            );
        }

        return $response;
    }
}