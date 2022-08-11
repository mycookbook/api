<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeStoreRequest;
use App\Services\RecipeService;
use Illuminate\Http\Request;

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
        $this->middleware('jwt.auth', ['except' => ['index', 'show', 'addClap']]);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myRecipes(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->service->index($request->get('user_id'));
    }

    /**
     * @param RecipeStoreRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(RecipeStoreRequest $request)
    {
//        $jwt->parseToken()->authenticate();
        //todo use Auth facade to authenticate jwt token

        return $this->service->store($request);
    }

    /**
     * @param Request $request
     * @param $recipeId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function update(Request $request, $recipeId)
    {
        return $this->service->update($request, $recipeId);
    }

    /**
     * @param $recipeId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function delete($recipeId)
    {
        return $this->service->delete($recipeId);
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
}
