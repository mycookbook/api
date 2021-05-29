<?php

namespace App\Http\Controllers\Requests\Recipe;

use App\Rules\JsonStructureRule;
use App\Rules\SupportedImageUrlFormatsRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$valid_request_payload = $this->validate(
			$request, [
				'name' => 'required|string',
				'ingredients' => 'required|json',
				'imgUrl' => ['required', new SupportedImageUrlFormatsRule()],
				'description' => 'required|string', //EDITOR to include steps
				'cookbookId' => 'required|exists:cookbooks,id',
				'summary' => 'required|string',
				'calorie_count' => 'integer',
				'cook_time' => 'required|date_format:Y-m-d H:i:s',
				'nutritional_detail' => [new JsonStructureRule()],
				'servings' => 'integer',
				'tags' => 'json'
			]
		);

		parent::__construct($request->merge($valid_request_payload));
	}
}
