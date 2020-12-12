<?php

namespace App\Http\Controllers\Requests\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreRequest extends Controller
{
	public function __construct(Request $request)
	{
		$valid_request_payload = $this->validate(
			$request, [
				'name' => 'required',
				'email' => 'required|email|unique:users',
				'password' => 'required|min:5'
			]
		);

		parent::__construct($request->merge($valid_request_payload));
	}
}