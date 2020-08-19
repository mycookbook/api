<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Services\RecipeService;
use App\Http\Controllers\Requests\Recipe\StoreRequest;

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
    public function index()
    {
        return $this->service->index();
    }

	/**
	 * Create recipe for user
	 *
	 * @param \App\Http\Controllers\Requests\Recipe\StoreRequest $request
	 * @param JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \Tymon\JWTAuth\Exceptions\JWTException
	 */
    public function store(StoreRequest $request, JWTAuth $jwt)
    {
		$jwt->parseToken()->authenticate();
		return $this->service->store($request->getParams());
    }

	/**
	 * Update Recipe
	 *
	 * @param Request $request
	 * @param int $recipeId
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function update(Request $request, $recipeId)
    {
        return $this->service->update($request, $recipeId);
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
        return $this->service->delete($recipeId);
    }

    /**
     * Find resource
     *
     * @param int $id identifier
     *
     * @return mixed
     */
    public function show($id)
    {
		return $this->service->show($id);
    }

	/**
	 * Increment Recipe count
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function addClap(Request $request)
	{
		$this->validate(
			$request, [
				'recipe_id' => 'required|exists:recipes,id'
			]
		);

		return $this->service->addClap($request->get('recipe_id'));
	}
}
