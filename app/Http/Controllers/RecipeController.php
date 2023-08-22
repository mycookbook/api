<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\CookbookModelNotFoundException;
use App\Http\Requests\RecipeStoreRequest;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWT;

/**
 * Class UserController
 */
class RecipeController extends Controller
{
    protected RecipeService $service;

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
            Log::debug('An error occured while creating this recipe', [
                'resource' => self::RECIPE_RESOURCE,
                'exception' => $exception
            ]);

            $message = "There was an error processing this request, please try again later.";
            $code = Response::HTTP_BAD_REQUEST;

            if ($exception->getCode() == 401) {
                $code = Response::HTTP_UNAUTHORIZED;
                $message = "You are not authorized to perform this action.";
            }

            return response()->json([
                'error' => $message,
            ], $code);
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
     * @param Request $request
     * @param $recipeId
     * @param JWT $jwtAuth
     * @return Application|ResponseFactory|JsonResponse|Response
     * @throws CookbookModelNotFoundException
     * @throws JWTException
     */
    public function destroy(Request $request, $recipeId, JWT $jwtAuth)
    {
        if (
            $jwtAuth->parseToken()->check() &&
            $request->user()->isSuper()
        ) {
            return $this->service->delete($request->user(), $recipeId);
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function report(Request $request, JWT $jwtAuth)
    {
        if ($jwtAuth->parseToken()->check()) {
            $recipe = Recipe::find($request->get('recipe_id'));

            if ($recipe instanceof Recipe) {
                $recipe->update(['is_reported' => 1]);
                return response()->json(['message' => 'feedback submitted.']);
            }

            Log::debug(
                'Error reporting recipe',
                [
                    'message' => 'Invalid recipe id',
                    'recipe_id' => $request->get('recipe_id')
                ]
            );

            return $this->errorResponse([
                'message' => 'There was an error processing this request. Please try again later.'
            ]);
        }
    }
}
