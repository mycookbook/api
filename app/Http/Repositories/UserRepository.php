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
        $users = User::with('cookbooks', 'recipes')->get();

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

        $user->name_slug = slugify($request->name);

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
     * @param $username
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function show($username)
    {
        $user = self::findWhere('name_slug', $username);

        if (!$user) {
            return $response = response(
                [
                    'error' => 'Resource must have been renamed or removed.',
                ], 404
            );
        }

        $user = new User();

        return response(
            [
                "data" => $user->where('name_slug', $username)->firstOrFail(),
            ], 200
        );
    }

    /**
     * Implement a full/partial update
     *
     * @param Request $request request
     * @param $username
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function update(Request $request, $username)
    {
        $user = self::findWhere('name_slug', $username);

        if (!$user) {
            return $response = response(
                [
                    'error' => 'Resource must have been renamed or removed.',
                ], 404
            );
        }


        $user = new User();
        $record = $user->where('name_slug', $username)->firstOrFail();

        $record->name_slug = slugify($request->name);

        try {
            $updated = $record->update($request->except(['email']));
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
        $user = new User();

        try {
            $response = $user->findorFail($id);
        } catch(\Exception $e) {
            $response = response(
                [
                    'error' => $e->getMessage(),
                ], 404
            );
        }

        return $response;
    }

    /**
     * @param $column
     * @param $key
     *
     * @return \Illuminate\Http\Response|\Illuminate\Support\Collection|\Laravel\Lumen\Http\ResponseFactory
     */
    public static function findWhere($column, $key)
    {
        try {
            $user = new User();
            $response = $user->where($column, $key)->exists();
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