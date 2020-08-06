<?php

namespace App\Http\Controllers\Requests\Cookbook;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\UnprocessibleEntityException;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'name' => 'required',
				'description' => 'required|min:126',
				'bookCoverImg' => 'required|url',
				'categories' => 'required|json',
				'flag_id' => 'required|exists:flags,id'
			]
		);

		$categories = json_decode($request->get('categories'));

		//strip duplicates if exists in categories
		$request->merge([
			'categories' => array_unique($categories),
		]);

		foreach ($request->get('categories') as $category) {
			if (!Category::find($category)) {
				throw new UnprocessibleEntityException('Category does not exist', Response::HTTP_UNPROCESSABLE_ENTITY);
			}
		}

		parent::__construct($request);
	}
}
