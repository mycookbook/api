<?php

use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

/**
 * Class UserControllerTest
 */
class RecipeControllerTest extends \TestCase
{
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
			'POST', '/api/v1/auth/signup', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/signin', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'POST', '/api/v1/recipes', [
			'title' => 'sample cookbook',
			'ingredients' => 'ttt', 'xxx',
			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
			'bookCoverImg' => 'https://cover-image-url',
			'summary' => Str::random(100),
			'imgUrl' => 'http://sample-url',
			'cookbookId' => $this->createCookbook()->id,
			'category_id' => $this->category->id,
			'flag_id' => $this->flag->id,
			'nutritional_detail' => 'sample details',
			'calorie_count' => 1200
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson([
			'created' => true
		])->seeStatusCode(201);
	}

	/**
	 * @test
	 */
	public function it_cannot_create_a_recipe_for_an_unauthenticated_user()
	{
		//refers to a request w/o a valid token
		$this->json(
			'POST', '/api/v1/cookbooks', [
			'title' => 'sample cookbook',
			'ingredients' => 'ttt', 'xxx',
			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
			'bookCoverImg' => 'https://cover-image-url',
			'summary' => Str::random(100),
			'imgUrl' => 'http://sample-url',
			'cookbookId' => $this->createCookbook()->id,
			'category_id' => $this->category->id,
			'flag_id' => $this->flag->id,
			'nutritional_detail' => 'sample details',
			'calorie_count' => 1200
		], [
				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
			]
		)->seeJson([
			'status' => "error",
			'message' => "Token is invalid"
		])->seeStatusCode(401);
	}
}