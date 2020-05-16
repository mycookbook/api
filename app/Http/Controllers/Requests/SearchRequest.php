<?php

namespace App\Http\Controllers\Requests;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'query' => 'required',
			]
		);

		parent::__construct($request);
	}
}