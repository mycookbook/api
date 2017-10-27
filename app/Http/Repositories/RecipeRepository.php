<?php

namespace App\Http\Repositories;

use App\User;
use App\Recipe;
use App\Cookbook;
use Tymon\JWTAuth\JWTAuth;
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
     * @param JWTAuth $jwt auth-jwt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($jwt)
    {
        return response(
            [
                'data' =>  Recipe::with('Cookbook')
                    ->where('user_id', $jwt->toUser()->id)
                    ->get()
                    ->toArray()
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
                $recipe->save();

                $data = $recipe;
                $statusCode = 201;
            }
        } catch(\Exception $e){
            $data = null;
            $statusCode = 404;
        }

        return response(
            [
                "data" => $data,
                'status' => $data ? 'success' : 'error or unknown cookbook.'
            ], $statusCode ?? 200
        );
    }

    /**
     * Update recipe
     *
     * @param Request $request  request
     * @param int     $recipeId recipeid
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function update($request, $id)
    {
        try {
            $recipe = Recipe::findorFail($id);
            $recipe->update($request->all());
        } catch(\Exception $e) {
            $recipe = null;
            $statusCode = 404;
        }

        return response(
            [
                "data" => $recipe,
                "updated" => $recipe ? true : "error",
            ], $statusCode ?? 204
        );
    }

    /**
     * Delete recipe
     *
     * @param int $recipeId recipeId
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function delete($id)
    {
        try {
            $recipe = Recipe::findorFail($id);
            $deleted = $recipe->delete();
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
