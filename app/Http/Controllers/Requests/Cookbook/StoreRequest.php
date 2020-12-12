<?php

namespace App\Http\Controllers\Requests\Cookbook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$categoriesArr = json_decode($request->get('categories'));

		// convert to array
		$request->merge([
			'categories' => $categoriesArr
		]);

		$this->validate(
			$request, [
				'name' => 'required',
				'description' => 'required|min:126',
				'bookCoverImg' => 'required|img_url',
				'categories' => 'required|array',
				'categories.*' => 'exists:categories,id',
				'flag_id' => 'required|exists:flags,id'
			]
		);

		//strip duplicates if exists in categories
		$request->merge([
			'categories' => array_unique($categoriesArr),
		]);

		parent::__construct($request);
	}
}
