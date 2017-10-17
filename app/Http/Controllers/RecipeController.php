<?php

namespace App\Http\Controllers;

use App\Recipe;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class RecipeController extends Controller
{
    /**
     * Constructor
     *
     * @param JWTAuth $jwt auth-jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->user = $this->jwt->parseToken()->authenticate();
    }

    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response(
            [
                'data' =>  Recipe::with('Cookbook')
                    ->where('user_id', $this->jwt->toUser()->id)
                    ->get()
                    ->toArray()
            ]
        );
    }

    /**
     * Create recipe for user
     *
     * @param Request            $request  Form input
     * @param CookbookController $cookbook cookbook
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, CookbookController $cookbook)
    {
        $this->validate(
            $request, [
                'name' => 'required',
                'ingredients' => 'required',
                'url' => 'required',
                'description' => 'required',
                'cookbookId' => 'required'
            ]
        );

        $recipe = new Recipe(
            [
                'name' => $request->name,
                'description' => $request->description,
                'imgUrl' => $request->url,
                'ingredients' => $request->ingredients,
                'user_id' => $this->user->id
            ]
        );

        try {
            if ($cookbook::cookbookExist($request->cookbookId)) {
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
     * Update Recipe
     *
     * @param Request $request  request
     * @param int     $recipeId recipeId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $recipeId)
    {
        try {
            $recipe = self::recipeExist($recipeId);
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
     * @param int $recipeId recipe
     *
     * @return string
     */
    public function delete($recipeId)
    {
        try {
            $recipe = self::recipeExist($recipeId);
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

    /**
     * Find the cookbook
     *
     * @param int $recipeId $recipeId
     *
     * @return mixed
     */
    protected static function recipeExist($recipeId)
    {
        return Recipe::findorFail($recipeId);
    }
}
