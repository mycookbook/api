<?php

namespace App\Services;

use App\Recipe;
use Carbon\Carbon;
use App\Cookbook;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
	 */
    public function show($id)
	{
		return Recipe::with('User', 'Cookbook')
			->where('id', $id)
			->orWhere('slug', $id)
			->firstOrFail();
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

		$recipe = new Recipe($request->all());
		$recipe->name = $request->title;
        $recipe->slug = slugify($request->title);
		$recipe->user_id = $user->id;
		$cookbook = Cookbook::findOrfail($request->cookbookId);
		$recipe->cookbook_id = $cookbook->id;

        return response([
        	"created" => $recipe->save()
		], Response::HTTP_CREATED);
    }

	/**
	 * Update recipe
	 *
	 * @param \Illuminate\Http\Request; $request
	 * @param int $id
	 *
	 * @return Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function update(Request $request, $id)
    {
    	//TODO: if user dont own recipe, can update it

		$recipe = Recipe::findOrfail($id);

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
     */
    public function delete($id)
    {
    	//TODO: if user dont own recipe, cannot delete it

		$recipe = Recipe::findOrfail($id);

		return response(
			[
				'deleted' => $recipe->delete()
			], Response::HTTP_ACCEPTED
		);
    }
}
