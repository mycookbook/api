<?php

namespace App\Http\Controllers\Requests\Recipe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'title' => 'required|string',
				'ingredients' => 'required',
				'imgUrl' => 'required|url',
				'description' => 'required|string',
				'cookbookId' => 'required|exists:cookbooks,id',
				'summary' => 'required|min:100',
				'nutritional_detail' => 'required',
				'calorie_count' => 'integer',
				'cook_time' => 'required'
			]
		);

		parent::__construct($request);
	}
}