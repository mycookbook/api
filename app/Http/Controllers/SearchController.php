<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Requests\SearchRequest;

class SearchController extends Controller
{
	/**
	 * @param SearchRequest $request
	 *
	 * @return mixed
	 */
	public function fetch(SearchRequest $request)
	{
		$q = $request->getParams()->input('query');

		$cookbooks = DB::table('cookbooks')
			->select([
				'cookbooks.id',
				'cookbooks.name',
				DB::raw('SUBSTR(cookbooks.description,1,300) as description'),
				'cookbooks.created_at',
				'cookbooks.bookCoverImg',
				'cookbooks.resource_type',
				'users.name AS author'
			])
			->leftJoin('users', 'users.id', '=', 'cookbooks.user_id')
			->whereRaw("MATCH(cookbooks.name,cookbooks.description) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipes = DB::table('recipes')
			->select([
				'recipes.id',
				'recipes.name',
				'recipes.description',
				'recipes.summary',
				'recipes.ingredients',
				'recipes.resource_type',
				'recipes.nutritional_detail',
				'recipes.imgUrl',
				'recipes.created_at',
				'users.name AS author'
			])
			->leftJoin('users', 'users.id', '=', 'recipes.user_id')
			->whereRaw("MATCH(recipes.name,recipes.description,recipes.ingredients,recipes.nutritional_detail,recipes.summary) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipe_variations = DB::table('recipe_variations')
			->select([
				'recipe_variations.id',
				'recipe_variations.name',
				'recipe_variations.description',
				'recipe_variations.ingredients',
				'recipe_variations.resource_type',
				'recipe_variations.imgUrl',
				'recipe_variations.created_at',
				'recipe_variations.name AS author',
				'recipe_variations.recipe_id',
			])->whereRaw("MATCH(name,description,ingredients) AGAINST(? IN BOOLEAN MODE)", array($q));

		return response()->json([
			'response' => $cookbooks->get()->merge($recipes->get())->merge($recipe_variations->get())
		]);
	}

	/**
	 * Get the user meta data and write to a csv file for ML purposes
	 *
	 * @param Request $request
	 */
	public function writeToCsv(Request $request)
	{
		$csv = $request->only([
			'city',
			'country',
			'ip',
			'keyword',
			'loc',
			'timezone'
		]);

		$csv['server_time'] = \Carbon\Carbon::now()->toDateTimeString();

		$file_open = fopen('keywords.csv', 'a+');

		fputcsv($file_open, $csv);
	}
}
