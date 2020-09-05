<?php

namespace App\Services;

use App\User;
use App\UserContactDetail;
use Illuminate\Http\Request;
use App\Interfaces\serviceInterface;

class UserContactDetailsService implements serviceInterface
{
	public function __construct(Request $request)
	{
		$user = User::findorFail($request->get('user_id'));
	}

	public function index()
	{
		// TODO: Implement index() method.
	}

	public function show($option)
	{
		// TODO: Implement show() method.
	}

	public function store(Request $request)
	{
		$detail = new UserContactDetail($request->only([
			'visibility',
			'user_id',
			'facebook',
			'twitter',
			'instagram',
			'skype',
			'office_address',
			'phone',
			'calendly',
			'skype',
			'website'
		]));
		$detail->save();
	}

	public function update(Request $request, $option)
	{
		// TODO: Implement update() method.
	}

	function get($q)
	{
		// TODO: Implement get() method.
	}
}