<?php


namespace App\Services;

use App\Category;
use Illuminate\Http\Request;
use App\Interfaces\serviceInterface;
use Illuminate\Http\Response;

class CategoryService implements serviceInterface
{
	public function index()
	{
		return response()->json(
			[
				'data' =>  Category::with('Cookbooks')->get()
			], Response::HTTP_OK
		);
	}

	public function show($option)
	{
		// TODO: Implement show() method.
	}

	public function store(Request $request)
	{
		// TODO: Implement store() method.
	}

	public function update(Request $request, $option)
	{
		// TODO: Implement update() method.
	}

	function findWhere($q)
	{
		// TODO: Implement get() method.
	}
}