<?php

namespace Integration\Requests\Cookbook;

use App\Flag;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\Cookbook\StoreRequest;

class StoreRequestTest extends \TestCase
{

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_request_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$storeRequest = new StoreRequest(new Request([]));
	}

	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);

		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);

		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_title_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => '',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_title_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_description_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => '',
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_description_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_description_is_less_than_126_characters_long()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(125),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_bookCoverImg_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => '',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_bookCoverImg_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_bookCoverImg_is_not_a_valid_url()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'invalid-url',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_categories_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => null,
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_categories_is_invalid_json()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => "invalid-json",
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_given_categories_does_not_exist()
	{
		//TODO: also what does it do if the given categories are duplicates?

		$this->expectException(\App\Exceptions\UnprocessibleEntityException::class);

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([0]),
			'flag_id' => $flag->id
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_flag_id_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => ''
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_flag_id_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_flag_id_does_not_exist()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => 0
		]));
	}

	/**
	 * @test
	 */
	public function it_returns_the_request_object()
	{
		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$requestData = [
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'categories' => json_encode([$category->id]),
			'flag_id' => $flag->id
		];

		$storeRequest = new StoreRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['name'], $storeRequest->getParams()->input('name'));
		$this->assertSame($requestData['description'], $storeRequest->getParams()->input('description'));
		$this->assertSame($requestData['bookCoverImg'], $storeRequest->getParams()->input('bookCoverImg'));
		$this->assertSame($requestData['flag_id'], $storeRequest->getParams()->input('flag_id'));
	}
}
