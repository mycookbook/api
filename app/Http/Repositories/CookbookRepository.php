<?php

namespace App\Http\Repositories;

use App\Cookbook;
use App\Http\Contracts\Repository;

/**
 * Class CookbookRepository
 */
class CookbookRepository implements Repository
{

    /**
     * Return all cookbooks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response(
            [
                'data' =>  Cookbook::with('Recipes', 'Users')
                    ->paginate(100)
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

        $cookbook->users()->attach($user->id);

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
            $updated = $cookbook->update($request->all());
            $statusCode =  $updated ? 202 : 422;
            $status = "success";
        } catch(\Exception $e) {
            $updated = false;
            $statusCode = 404;
            $status = ['error' => $e->getMessage()];
        }

        return response(
            [
                'updated' => $updated,
                'status' => $status
            ], $statusCode
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
            $status = "success";
        } catch (\Exception $e) {
            $deleted = false;
            $statusCode = 404;
            $status = ['error' => $e->getMessage()];
        }

        return response(
            [
                'deleted' => $deleted,
                'status' => $status,
            ], $statusCode
        );
    }
}