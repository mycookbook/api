<?php

namespace Functional\Controllers\Recipe;

use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutMiddleware;

/**
 * Class UserControllerTest
 */
class RecipeControllerTest extends \TestCase
{
	use WithoutMiddleware;
    use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_can_retrieve_all_recipes_and_respond_with_a_200_status_code()
	{
		$this->json('GET', '/api/v1/recipes')
			->seeJsonStructure(['data'])
			->assertResponseStatus(Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_retrieving_a_recipe_that_does_not_exist()
	{
		$this->json('GET', '/api/v1/recipes/0')
			->seeJson(['error' => "Record Not found."])
			->assertResponseStatus(Response::HTTP_NOT_FOUND);
	}

	/**
	 * @test
	 */
	public function it_can_create_a_recipe_for_an_authenticated_user()
	{
		//refers to a request that contains a valid token
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/recipes', [
			'title' => 'sample recipe',
			'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
			'summary' => Str::random(100),
			'imgUrl' => 'http://lorempixel.com/400/200/',
			'cookbookId' => $this->createCookbook()->id,
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'prep_time' => '2020-04-07 00:00:10',
			'servings' => 2
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson([
			'created' => true
		])->seeStatusCode(Response::HTTP_CREATED);
	}

	/**
	 * @test
	 */
	public function it_can_update_a_recipe_for_an_authenticated_user()
	{
		$recipe = $this->createRecipe();

		//refers to a request that contains a valid token
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'PUT', '/api/v1/recipes' . '/' . $recipe->id , [
			'title' => 'new title'
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson([
			'updated' => true
		])->seeStatusCode(Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function an_authenticated_user_can_delete_a_recipe_they_own()
	{
		$recipe = $this->createRecipe();

		//refers to a request that contains a valid token
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'DELETE', '/api/v1/recipes' . '/' . $recipe->id , [
			'title' => 'new title'
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson([
			'deleted' => true
		])->seeStatusCode(Response::HTTP_ACCEPTED);
	}

	public function an_authenticated_user_can_update_a_recipe_they_dont_own() {}
	public function an_authenticated_user_cannot_delete_a_recipe_they_dont_own() {}
}
