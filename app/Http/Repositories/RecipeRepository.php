<?php

namespace App\Http\Repositories;

use App\User;
use App\Recipe;
use App\Cookbook;
use Illuminate\Http\Request;
use App\Http\Contracts\Repository;

/**
 * Class RecipeRepository
 */
class RecipeRepository implements Repository
{
    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response(
            [
                'data' =>  Recipe::with('Cookbook', 'User')
                    ->paginate(100)
            ]
        );
    }

    /**
     * Perform POST action to create new recipe resource
     *
     * @param Request $request request
     * @param User    $user    user
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function store($request, $user)
    {
        $recipe = new Recipe(
            [
                'name' => $request->name,
                'description' => $request->description,
                'imgUrl' => $request->url,
                'ingredients' => $request->ingredients,
                'user_id' => $user->id
            ]
        );

        try {
            if (Cookbook::findOrFail($request->cookbookId)) {
                $recipe->cookbook_id = $request->cookbookId;
                $recipe->saveOrFail();

                $data = $recipe;
                $statusCode = 201;
            }
        } catch(\Exception $e){
            $data = null;
            $statusCode = 404;
            $msg = $e->getMessage();
        }

        return response(
            [
                "data" => $data,
                'status' => $data ? 'success' : $msg
            ], $statusCode ?? 200
        );
    }

    /**
     * Update recipe
     *
     * @param Request $request request
     * @param int     $id      recipeid
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update($request, $id)
    {
        try {
            $recipe = Recipe::findorFail($id);
            $updated = $recipe->update($request->all());
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
     * Delete recipe
     *
     * @param int $id recipeId
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
        try {
            $recipe = Recipe::findorFail($id);
            $deleted = $recipe->delete();
            $statusCode = $deleted ? 202 : 422;
            $status = "success";
        } catch(\Exception $e) {
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
