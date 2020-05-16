<?php

namespace App\Http\Controllers;

use App\Subscriber;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
	/**
	 * @param Request $request
	 */
	public function store(Request $request)
	{
		$this->validate(
			$request, [
				'email' => 'required'
			]
		);

		$subscriber = new Subscriber();
		$subscriber->email = $request->email;
		$subscriber->save();

		return response()->json(
			[
				'response' => [
					'created' => true,
					'data' => null,
					'status' => "success",
				]
			], 200
		);
	}
}
