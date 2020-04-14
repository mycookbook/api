<?php

namespace App\Http\Controllers\Requests\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'name' => 'required',
				'email' => 'required|email|unique:users',
				'password' => 'required|min:5'
			]
		);

		$this->params = $request->all();
	}
}