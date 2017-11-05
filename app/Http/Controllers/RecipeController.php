<?php

namespace App\Http\Controllers;

use App\Recipe;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Repositories\RecipeRepository;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class RecipeController extends Controller
{
    protected $recipe;

    /**
     * Constructor
     *
     * @param RecipeRepository $recipe recipeRepository
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function __construct(RecipeRepository $recipe)
    {
        $this->middleware('jwt.auth');
        $this->recipe = $recipe;
    }

    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->recipe->index();
    }

    /**
     * Create recipe for user
     *
     * @param Request $request Form input
     * @param JWTAuth $jwt     jwt-auth
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, JWTAuth $jwt)
    {
        $this->validate(
            $request, [
                'name' => 'required|string',
                'ingredients' => 'required',
                'url' => 'required|url',
                'description' => 'required|string',
                'cookbookId' => 'required'
            ]
        );

        $user = $jwt->parseToken()->authenticate();

        return $this->recipe->store($request, $user);
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
        return $this->recipe->update($request, $recipeId);
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
        return $this->recipe->delete($recipeId);
    }

    /**
     * Find resource
     *
     * @param int $id identifier
     *
     * @return mixed
     */
    public function find($id)
    {
        try {
            $response = Recipe::with('User', 'Cookbook')->findOrFail($id);
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
