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
			->whereRaw("MATCH(name,description) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipes = DB::table('recipes')
			->whereRaw("MATCH(name,description,ingredients,nutritional_detail,summary) AGAINST(? IN BOOLEAN MODE)", array($q));

		$recipe_variations = DB::table('recipe_variations')
			->whereRaw("MATCH(name,description,ingredients) AGAINST(? IN BOOLEAN MODE)", array($q));

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
