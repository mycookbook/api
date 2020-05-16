<?php

namespace App\Http\Controllers;

use App\Cookbook;
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

		return Cookbook::whereRaw(
			"MATCH(name,description) AGAINST(? IN BOOLEAN MODE)",
			array($q)
		)->get();
	}
}