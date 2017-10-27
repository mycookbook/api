<?php

namespace App\Http\Controllers;

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
     * @param JWTAuth          $jwt    auth-jwt
     * @param RecipeRepository $recipe recipeRepository
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function __construct(JWTAuth $jwt, RecipeRepository $recipe)
    {
        $this->jwt = $jwt;
        $this->user = $this->jwt->parseToken()->authenticate();
        $this->recipe = $recipe;
    }

    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->recipe->index($this->jwt);
    }

    /**
     * Create recipe for user
     *
     * @param Request $request Form input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
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

        return $this->recipe->store($request, $this->user);
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
}
