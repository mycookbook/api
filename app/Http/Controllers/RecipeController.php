<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Http\Requests\RecipeStoreRequest;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWT;

/**
 * Class UserController
 */
#[AllowDynamicProperties] class RecipeController extends Controller
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
     * @return JsonResponse
     */
    public function index()
    {
        return $this->successResponse(['data' => $this->service->index()]);
    }

    /**
     * @param $recipeId
     * @return JsonResponse
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function show($recipeId): JsonResponse
    {
        return $this->successResponse(['data' => $this->service->show($recipeId)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\CookbookModelNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addClap(Request $request): JsonResponse
    {
        $this->validate(
            $request, [
                'recipe_id' => 'required|exists:recipes,id',
            ]
        );

        return ($recipe = $this->service->addClap($request->get('recipe_id'))) ?
            /** @phpstan-ignore-next-line  */
            $this->successResponse(['updated' => true, 'claps' => $recipe->claps]) :
            $this->errorResponse(['error' => 'There was an error processing this request. Please try again.']);
    }

    /**
     * @param Request $request
     * @param JWT $jwtAuth
     * @return JsonResponse
     * @throws JWTException
     */
    public function myRecipes(Request $request, JWT $jwtAuth): JsonResponse
    {
        if ($jwtAuth->parseToken()->check()) {
            return $this->successResponse([
                'data' => $this->service->index($request->get('user_id'))
            ]);
        }

        return response()->json([
            'error', 'You are not authorized to access this resource.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function store(RecipeStoreRequest $request, JWT $jwtAuth)
    {
        try {
            $jwtAuth->parseToken()->check();

            return $this->service->store($request) ?
                $this->successResponse(['created' => true],ResponseAlias::HTTP_CREATED) :
                $this->errorResponse(['created' => false]);

        } catch (\Exception $exception) {
            Log::debug('An error occurred while creating this recipe', [
                'resource' => self::RECIPE_RESOURCE,
                'exception' => $exception
            ]);

            $message = "There was an error processing this request, please try again later.";
            $code = ResponseAlias::HTTP_BAD_REQUEST;

            if ($exception->getCode() == 401) {
                $code = ResponseAlias::HTTP_UNAUTHORIZED;
                $message = "You are not authorized to perform this action.";
            }

            return response()->json([
                'error' => $message,
            ], $code);
        }
    }

    public function update(Request $request, $recipeId, JWT $jwtAuth)
    {
        if ($jwtAuth->parseToken()->check() && $request->user()->ownRecipe($recipeId)) {
            if ($this->service->update($request, $recipeId)) {
                return $this->successResponse(['updated' => true]);
            }

            return $this->errorResponse(['updated' => false]);
        }

        return response()->json([
            'error' => 'You are not authorized to access this resource.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function destroy(Request $request, $recipeId, JWT $jwtAuth)
    {
        if ($jwtAuth->parseToken()->check() && $request->user()->isSuper()) {
            if ($this->service->delete($request->user(), $recipeId)) {
                return $this->successResponse(['deleted' => true]);
            }

            return $this->errorResponse(['deleted' => false]);
        }

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function report(Request $request, JWT $jwtAuth): JsonResponse
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

        return response()->json([
            'error' => 'You are not authorized to perform this action.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
