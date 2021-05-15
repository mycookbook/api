<?php

namespace App\Http\Controllers\Requests\Cookbook;

use App\Rules\MaxAllowedRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\SupportedImageUrlFormatsRule;
use Illuminate\Validation\ValidationException;

class StoreRequest extends Controller
{
	/**
	 * StoreRequest constructor.
	 * @param Request $request
	 * @throws ValidationException
	 */
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'name' => 'required',
				'description' => 'required|min:126',
				'bookCoverImg' => ['required', new SupportedImageUrlFormatsRule()],
				'category_id' => 'required|exists:categories,id',
				'categories' => ['exists:categories,id', new MaxAllowedRule(2)],
				'flag_id' => 'required|exists:flags,id'
			]
		);

		$categories = explode(",", $request->get("categories"));
		$categories[] = $request->get("category_id");

		$request->merge([
			"categories" => array_unique($categories)
		]);

		parent::__construct($request);
	}
}
