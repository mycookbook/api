<?php

namespace Integration\Requests\Recipe;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\Recipe\StoreRequest;

class StoreRequestTest extends \TestCase
{
	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_request_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new StoreRequest(new Request([]));
	}

	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$cookbook = $this->createCookbook();

		$request = new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_title_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => '',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_ingredients_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => '',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_imgUrl_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => '',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_imgUrl_is_not_a_valid_url()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'invalid-url',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_description_field_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => '',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_cookbook_id_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => '',
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_cookbook_id_is_invalid()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => '',
			'cookbookId' => 0,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_summary_field_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => '',
			'cookbookId' => $cookbook->id,
			'summary' => '',
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_summary_field_is_less_than_100_characters()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => 'short summary',
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_nutritional_detail_field_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '',
			'calorie_count' => 1200
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_calorie_count_field_is_not_an_integer()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 'a string'
		]));
	}

	/**
	 * @test
	 */
	public function it_returns_the_request_object()
	{
		$requestData = [
			'title' => 'sample title',
			'ingredients' => 'ttt', 'xxx',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $this->createCookbook()->id,
			'summary' => Str::random(100),
			'nutritional_detail' => 'sample detail',
			'calorie_count' => 1200
		];

		$storeRequest = new StoreRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['title'], $storeRequest->getParams()->input('title'));
		$this->assertSame($requestData['ingredients'], $storeRequest->getParams()->input('ingredients'));
		$this->assertSame($requestData['imgUrl'], $storeRequest->getParams()->input('imgUrl'));
		$this->assertSame($requestData['description'], $storeRequest->getParams()->input('description'));
		$this->assertSame($requestData['cookbookId'], $storeRequest->getParams()->input('cookbookId'));
		$this->assertSame($requestData['summary'], $storeRequest->getParams()->input('summary'));
		$this->assertSame($requestData['nutritional_detail'], $storeRequest->getParams()->input('nutritional_detail'));
		$this->assertSame($requestData['calorie_count'], $storeRequest->getParams()->input('calorie_count'));
	}
}