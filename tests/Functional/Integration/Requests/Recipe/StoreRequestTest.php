<?php

namespace Functional\Integration\Requests\Recipe;

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
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => '',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => 'sample title',
			'ingredients' => '',
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_ingredients_is_not_a_valid_json()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => 'invalid-json',
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'flag_id' => $this->createFlag()->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => '',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_imgUrl_is_not_a_valid_img_url()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => '',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_cookbook_id_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => '',
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_cookbook_id_is_invalid()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => '',
			'cookbookId' => 0,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => '',
			'cookbookId' => $cookbook->id,
			'summary' => '',
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
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
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'calorie_count' => 'a string',
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_cook_time_is_not_given()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 200,
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_cook_time_is_not_a_valid_datetime_format()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 200,
			'cook_time' => 'invalid datetime format',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_servings_is_not_an_integer()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$cookbook = $this->createCookbook();

		new StoreRequest(new Request([
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'short description',
			'cookbookId' => $cookbook->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 200,
			'cook_time' => 'invalid datetime format',
			'servings' => 'not an integer',
			'tags' => json_encode(['trending'])
		]));
	}

	/**
	 * @test
	 */
	public function it_returns_the_request_object()
	{
		$requestData = [
			'name' => 'sample title',
			'ingredients' => json_encode(["data" => ["ingredient1", "ingredient2"]]),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'description' => 'sample description',
			'cookbookId' => $this->createCookbook()->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2,
			'tags' => json_encode(['trending'])
		];

		$storeRequest = new StoreRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['name'], $storeRequest->getParams()->input('name'));
		$this->assertSame($requestData['ingredients'], $storeRequest->getParams()->input('ingredients'));
		$this->assertSame($requestData['imgUrl'], $storeRequest->getParams()->input('imgUrl'));
		$this->assertSame($requestData['description'], $storeRequest->getParams()->input('description'));
		$this->assertSame($requestData['cookbookId'], $storeRequest->getParams()->input('cookbookId'));
		$this->assertSame($requestData['summary'], $storeRequest->getParams()->input('summary'));
		$this->assertSame($requestData['nutritional_detail'], $storeRequest->getParams()->input('nutritional_detail'));
		$this->assertSame($requestData['calorie_count'], $storeRequest->getParams()->input('calorie_count'));
	}
}