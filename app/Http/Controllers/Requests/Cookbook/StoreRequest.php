<?php

namespace App\Http\Controllers\Requests\Cookbook;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'name' => 'required',
				'description' => 'required|min:126',
				'bookCoverImg' => 'required|url',
				'category_id' => 'required|exists:categories,id',
				'flag_id' => 'required|exists:flags,id'
			]
		);

		parent::__construct($request);
	}
}
