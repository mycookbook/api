<?php

namespace App\Http\Repositories;

use App\Cookbook;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Contracts\Repository;
/**
 * Class CookbookRepository
 */
class CookbookRepository implements Repository
{
    /**
     * Return cookbooks
     *
     * @param JWTAuth $jwt auth-jwt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($jwt)
    {
        return response(
            [
                'data' =>  Cookbook::with('Recipes', 'User')
                    ->where('user_id', $jwt->toUser()->id)
                    ->get()
                    ->toArray()
            ]
        );
    }

    /**
     * Create cookbook resource
     *
     * @param Request $request request
     * @param User    $user    user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($request, $user)
    {
        $cookbook = new Cookbook(
            [
                'name' => $request->name,
                'description' => $request->description,
                'user_id' =>  $user->id
            ]
        );

        $data = $cookbook->save();

        $statusCode = $cookbook ? 201 : 422;

        return response()->json(
            [
                'response' => [
                    'created' => true,
                    'data' => Cookbook::findOrfail($cookbook),
                    'status' => $data ? "success" : "error",
                ]
            ], $statusCode
        );
    }

    /**
     * Update cookbook resource
     *
     * @param \App\Http\Contracts\Request $request request
     * @param int                         $id      identifier
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update($request, $id)
    {
        try {
            $cookbook = Cookbook::findOrFail($id);
            $cookbook->update($request->all());
        } catch(\Exception $e) {
            $cookbook = null;
            $statusCode = 404;
        }

        return response(
            [
                "data" => $cookbook,
                "status" => $cookbook ? "success" : "Not Found."
            ], $statusCode ?? 204
        );
    }

    /**
     * Delete Cookbook resource
     *
     * @param int $id identofier
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
        try {
            $cookbook = Cookbook::findOrFail($id);
            $deleted = $cookbook->delete();
            $statusCode = $deleted ? 202 : 422;
        } catch (\Exception $e) {
            $deleted = false;
            $statusCode = 404;
        }

        return response(
            [
                'deleted' => $deleted,
                'status' => $deleted ? "success" : "error",
            ], $statusCode
        );
    }
}