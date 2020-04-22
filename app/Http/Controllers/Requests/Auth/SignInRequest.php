<?php

namespace App\Http\Controllers\Requests\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignInRequest extends Controller
{
	public function __construct(Request $request)
	{
		$this->validate(
			$request, [
				'email' => 'required',
				'password' => 'required'
			]
		);

		parent::__construct($request);
	}
}
