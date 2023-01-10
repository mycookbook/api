<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeStoreRequest;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\JWT;

/**
 * Class UserController
 */
class RecipeController extends Controller
{
    /**
     * Constructor
     *
     * @param RecipeService $service
     */
    public function __construct(RecipeService $service)
    {
        $this->middleware('auth.guard')->except(['index', 'show', 'addClap']);

        $this->service = $service;
    }

    /**
     * Get all recipes belonging to a user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->service->index();
    }

    /**
     * @param $recipeId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function show($recipeId)
    {
        return $this->service->show($recipeId);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\CookbookModelNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addClap(Request $request)
    {
        $this->validate(
            $request, [
                'recipe_id' => 'required|exists:recipes,id',
            ]
        );

        return $this->service->addClap($request->get('recipe_id'));
    }

    /**
     * @param Request $request
     * @param JWT $jwtAuth
     * @return \Illuminate\Http\JsonResponse
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function myRecipes(Request $request, JWT $jwtAuth): \Illuminate\Http\JsonResponse
    {
        if ($jwtAuth->parseToken()->check()) {
            return $this->service->index($request->get('user_id'));
        }

        return response()->json([
            'error', 'You are not authorized to access this resource.'
        ], 401);
    }

    /**
     * @param RecipeStoreRequest $request
     * @param JWT $jwtAuth
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(RecipeStoreRequest $request, JWT $jwtAuth)
    {
        try {
            $jwtAuth->parseToken()->check();
            return $this->service->store($request);
        } catch (\Exception $exception) {
            Log::debug('An error occured while creating a recipe', [
                'resource' => self::RECIPE_RESOURCE,
                'exception' => $exception
            ]);

            return response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Request $request
     * @param $recipeId
     * @param JWT $jwtAuth
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \App\Exceptions\CookbookModelNotFoundException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function update(Request $request, $recipeId, JWT $jwtAuth)
    {
        if (
            $request->user()->ownRecipe($recipeId) &&
            $jwtAuth->parseToken()->check()
        ) {
            return $this->service->update($request, $recipeId);
        }

        return response()->json([
            'error' => 'You are not authorized to access this resource.'
        ], 401);
    }

    /**
     * @param $recipeId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function destroy($recipeId)
    {
        if (
            $request->user()->isSuper() &&
            $jwtAuth->parseToken()->check()
        ) {
            return $this->service->delete($recipeId);
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], 401);
    }
}
