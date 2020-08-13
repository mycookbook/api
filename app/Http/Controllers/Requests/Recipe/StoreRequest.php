<?php

namespace App\Http\Controllers\Requests\Recipe;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	protected $req_nutritional_detail = ["cal", "fat", "carbs", "protein"];

	public function __construct(Request $request)
	{
		$valid_request_payload = $this->validate(
			$request, [
				'title' => 'required|string',
				'ingredients' => 'required|json',
				'imgUrl' => 'required|url',
				'description' => 'required|string', //WSSYWIG EDITOR to include steps
				'cookbookId' => 'required|exists:cookbooks,id',
				'summary' => 'required|string',
				'calorie_count' => 'integer',
				'cook_time' => 'required|date_format:Y-m-d H:i:s',
				'nutritional_detail' => 'json|nutritional_detail_json_structure'
			]
		);

		//TODO: define json structure for nutritional details

		parent::__construct($request->merge($valid_request_payload));
	}
}
