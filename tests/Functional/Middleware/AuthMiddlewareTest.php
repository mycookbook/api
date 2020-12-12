<?php

namespace Functional\Middleware;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthMiddlewareTest extends \TestCase
{
//	use DatabaseMigrations;
//
//	/**
//	 * @test
//	 */
//	public function it_cannot_create_a_cookbook_for_an_unauthenticated_user()
//	{
//		$this->json(
//			'POST', '/api/v1/cookbooks', [
//			'name' => 'sample cookbook',
//			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
//			'bookCoverImg' => 'http://lorempixel.com/400/200/',
//			'categories' => json_encode([$this->createCategory()->id]),
//			'flag_id' => $this->createFlag()->id
//		], [
//				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
//			]
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(Response::HTTP_UNAUTHORIZED);
//	}
//
//	/**
//	 * @test
//	 */
//	public function it_cannot_update_a_cookbook_for_a_user_with_an_invalid_token()
//	{
//		$cookbook = $this->createCookbook();
//		$this->json(
//			'PUT', '/api/v1/cookbooks' . '/' . $cookbook->id, [
//			'name' => 'new title'
//		], [
//				'HTTP_Authorization' => 'Bearer' . 'invalid-token'
//			]
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(Response::HTTP_UNAUTHORIZED);
//	}
//
//	/**
//	 * @test
//	 */
//	public function it_cannot_delete_a_cookbook_for_a_user_with_an_invalid_token()
//	{
//		$cookbook = $this->createCookbook();
//		$this->json(
//			'DELETE', '/api/v1/cookbooks' . '/' . $cookbook->id,
//			[],
//			['HTTP_Authorization' => 'Bearer' . 'invalid-token']
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(401);
//	}
//
//	/**
//	 * @test
//	 */
//	public function it_cannot_create_a_recipe_for_an_unauthenticated_user()
//	{
//		//refers to a request w/o a valid token
//		$this->json(
//			'POST', '/api/v1/recipes', [
//			'title' => 'sample recipe',
//			'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
//			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
//			'summary' => Str::random(100),
//			'imgUrl' => 'http://lorempixel.com/400/200/',
//			'cookbookId' => $this->createCookbook()->id,
//			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
//			'calorie_count' => 1200,
//			'cook_time' => '2020-04-07 00:55:00',
//			'prep_time' => '2020-04-07 00:00:10',
//			'servings' => 2
//		], [
//				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
//			]
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(Response::HTTP_UNAUTHORIZED);
//	}
//
//	/**
//	 * @test
//	 */
//	public function an_unauthenticated_user_cannot_delete_a_recipe()
//	{
//		$recipe = $this->createRecipe();
//
//		//refers to a request w/o a valid token
//		$this->json(
//			'DELETE', '/api/v1/recipes' . '/' . $recipe->id, [
//			'title' => 'new title'
//		], [
//				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
//			]
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(Response::HTTP_UNAUTHORIZED);
//	}
//
//
//	/**
//	 * @test
//	 */
//	public function an_unauthenticated_user_cannot_update_a_recipe()
//	{
//		$recipe = $this->createRecipe();
//
//		//refers to a request w/o a valid token
//		$this->json(
//			'PUT', '/api/v1/recipes' . '/' . $recipe->id, [
//			'title' => 'new title'
//		], [
//				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
//			]
//		)->seeJson([
//			'status' => "error",
//			'message' => "Token is invalid"
//		])->seeStatusCode(Response::HTTP_UNAUTHORIZED);
//	}
}