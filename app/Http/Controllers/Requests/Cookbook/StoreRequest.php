<?php

namespace App\Http\Controllers\Requests\Cookbook;

use App\Category;
use App\Exceptions\UnprocessibleEntityException;
use App\Rules\MaxAllowedRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\SupportedImageUrlFormatsRule;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StoreRequest extends Controller
{
	/**
	 * StoreRequest constructor.
	 * @param Request $request
	 * @throws ValidationException
	 * @throws UnprocessibleEntityException
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

		for ($i=0;$i<count($categories);$i++) {
			if (!Category::find($categories[$i])){
				throw new UnprocessibleEntityException(
					'One of the selected additional category id is invalid.',
					Response::HTTP_UNPROCESSABLE_ENTITY
				);
			}
		}

		$request->merge([
			"categories" => array_unique($categories)
		]);

		parent::__construct($request);
	}
}
