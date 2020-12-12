<?php

namespace Functional\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\WithoutMiddleware;

class UnprocessibleentityExceptionTest extends \TestCase
{
	use WithoutMiddleware;

	/**
	 * It throws a 422 when the request is null
	 * @test
	 */
	public function testUnprocessibleentityException()
	{
		$response = $this->call('POST', '/api/v1/auth/register');

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the email is null/not given
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_email_is_null()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => 'test', 'email' => null, 'password' => 'test1234']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the email is not a valid email
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_email_is_not_valid_email_format()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => 'test', 'email' => 'invalid-email', 'password' => 'test1234']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the name is null/not given
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_name_is_null()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => null, 'email' => 'test@mail.com', 'password' => 'test1234']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the name is empty
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_name_is_empty()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => '', 'email' => 'test@mail.com', 'password' => 'test1234']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the password is null
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_password_is_null()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => 'test', 'email' => 'test@mail.com']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the password is empty
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_password_is_empty()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => 'test', 'email' => 'test@mail.com', 'password' => '']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * It throws a 422 when the password is less than the specified length
	 * @test
	 */
	public function it_throws_a_unprocessible_entity_exception_if_the_password_is_less_than_5_chars()
	{
		$response = $this->call('POST', '/api/v1/auth/register', ['name' => 'test', 'email' => 'test@mail.com', 'password' => '1234']);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_request_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json('POST', '/api/v1/cookbooks', [], ['HTTP_Authorization' => 'Bearer' . $token])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_name_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => '',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
			)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_description_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => 'test title',
				'description' => '',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_description_is_less_than_100_characters()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => '',
				'description' => 'less than 100 characters',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_cover_img_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => 'test title',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => '',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_cover_img_is_not_a_valid_url()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => '',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'invalid-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_categories_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => 'test title',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => '',
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_flag_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => 'test title',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => ''
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_cookbook_flag_does_not_exist_in_the_flags_table()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/cookbooks',
			[
				'name' => 'test title',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => 0
			], [
				'HTTP_Authorization' => 'Bearer' . $token]
		)->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_request_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json('POST', '/api/v1/recipes', [], ['HTTP_Authorization' => 'Bearer' . $token])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_title_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => '',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_ingredients_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test',
				'ingredients' => '',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_ingredients_is_given_but_not_in_json_format()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test',
				'ingredients' => array(),
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_ingredients_is_given_but_not_in_valid_json_format()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test',
				'ingredients' => "{'invalid-json'}",
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'https://cover-image-url',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_description_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => '',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => '',
				'bookCoverImg' => 'https://cover-image-url',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cover_img_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => '',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cover_img_is_invalid_url()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'invalid-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_summary_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => '',
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cookbook_id_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => '',
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cookbook_id_does_not_exist()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => 0,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_nutritional_detail_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => null,
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_nutritional_detail_is_not_json()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => 'not-json',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_nutritional_detail_is_not_valid_json()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => "{'invalid-json'}",
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_calorie_count_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => null,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_calorie_count_is_not_a_number()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 'not-a-number',
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cook_time_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => null,
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_cook_time_is_not_Y_m_d_H_i_s_date_format()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '07/04/2020',
				'servings' => 2
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_servings_is_not_given()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => null
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_recipe_servings_is_not_a_number()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$login = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($login->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST',
			'/api/v1/recipes',
			[
				'title' => 'test title',
				'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'summary' => Str::random(100),
				'imgUrl' => 'http://sample-url',
				'cookbookId' => $this->createCookbook()->id,
				'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
				'calorie_count' => 1200,
				'cook_time' => '2020-04-07 00:55:00',
				'servings' => 'not-a-number'
			], [
			'HTTP_Authorization' => 'Bearer' . $token
		])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
	}
}