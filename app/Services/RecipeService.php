<?php

namespace App\Services;

use App\Recipe;
use App\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\CookbookModelNotFoundException;

/**
 * Class RecipeService
 */
class RecipeService
{
	/**
	 * Get all recipes
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function index()
    {
		return response()->json(
			[
				'data' =>  Recipe::with('Cookbook', 'User') //eagerload with cookbook and user relationships
					->paginate(100)
			], Response::HTTP_OK
		);
    }

	/**
	 * Retrieve one Recipe
	 *
	 * @param $id
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
	 * @throws CookbookModelNotFoundException
	 */
    public function show($id)
	{
		$recipe = $this->get($id);

		if (!$recipe) {
			throw new CookbookModelNotFoundException();
		}

		return $recipe;
	}

	/**
	 * Creates a new Recipe
	 *
	 * @param \Illuminate\Http\Request; $request
	 *
	 * @return Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function store($request)
    {
		$user = $request->user();

		$payload = $request->only([
			'name',
			'ingredients',
			'imgUrl',
			'description',
			'cookbookId',
			'summary',
			'calorie_count',
			'cook_time',
			'nutritional_detail',
			'servings',
			'tags'
		]);

		$payload['slug'] = slugify($request->title);
		$payload['user_id'] = $user->id;

		$recipe = new Recipe($payload);

		$cookbook = Cookbook::findOrfail($request->cookbookId);
		$recipe->cookbook_id = $cookbook->id;

		//TODO:
		//if tags present, create tags instances and attach to recipe

        return response([
        	"created" => $recipe->save()
		], Response::HTTP_CREATED);
    }

	/**
	 * Update recipe
	 *
	 * @param Request $request
	 * @param int $id
	 *
	 * @return Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function update(Request $request, $id)
    {
    	//TODO: if user dont own recipe, can update it

		$recipe = $this->get($id);
//		$recipe->prep_time

		return response(
			[
				'updated' => $recipe->update($request->all()),
			],Response::HTTP_OK
		);
    }

	/**
	 * Delete recipe
	 *
	 * @param int $id recipeId
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function delete($id)
    {
    	//TODO: if user dont own recipe, cannot delete it

		$recipe = $this->get($id);

		return response(
			[
				'deleted' => $recipe->delete()
			], Response::HTTP_ACCEPTED
		);
    }

	/**
	 * Increment recipe claps count by 1 each time
	 *
	 * @param $recipeId
	 *
	 * @return Response|\Laravel\Lumen\Http\ResponseFactory
	 * @throws CookbookModelNotFoundException
	 */
    public function addClap($recipeId)
	{
		$recipe = $this->get($recipeId);

		$recipe->claps = $recipe->claps + 1;
		$recipe->save();

		return response(
			[
				'updated' => true,
				'claps' => $recipe->claps
			],Response::HTTP_OK
		);
	}

	/**
	 * Find recipe record
	 *
	 * @param $q
	 * @return mixed
	 * @throws CookbookModelNotFoundException
	 */
	public function get($q)
	{
		$record = Recipe::with('User', 'Cookbook')
			->where('id', $q)
			->orWhere('slug', $q)
			->first();

		if (!$record) {
			throw new CookbookModelNotFoundException();
		}

		return $record;
	}
}
