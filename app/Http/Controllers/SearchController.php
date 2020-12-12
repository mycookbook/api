<?php

namespace App\Http\Controllers;

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
			->whereRaw("MATCH(name,description) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipes = DB::table('recipes')
			->whereRaw("MATCH(name,description,ingredients,nutritional_detail,summary) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipe_variations = DB::table('recipe_variations')
			->whereRaw("MATCH(name,description,ingredients) AGAINST(? IN BOOLEAN MODE)", array($q));

		return response()->json([
			'response' => $cookbooks->get()->merge($recipes->get())->merge($recipe_variations->get())
		]);
	}
}
