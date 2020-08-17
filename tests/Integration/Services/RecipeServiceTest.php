<?php

namespace Integration\Services;

use App\Recipe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RecipeService;
use App\Http\Controllers\Requests\Recipe\StoreRequest;

class RecipeServiceTest extends \TestCase
{
	/**
	 * @test
	 */
	public function it_responds_with_a_200_when_retrieving_all_recipes()
	{
		$service = new RecipeService();
		$response = $service->index();
		$this->assertSame(Response::HTTP_OK, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_an_unauthenticated_user_attempts_to_create_a_recipe()
	{
		$this->expectException(\ErrorException::class);

		$request = new StoreRequest(new Request([
			'title' => 'sample title',
			'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $this->createCookbook()->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2
		]));

		$service = new RecipeService();
		$service->store($request->getParams());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_201_when_an_authenticated_user_attempts_to_create_a_recipe()
	{
		$request = new Request([
			'title' => 'sample title',
			'ingredients' => '{"data": [ "onions", "red pepper", "vegetable oil" ]}',
			'imgUrl' => 'http://sample-url',
			'description' => 'sample description',
			'cookbookId' => $this->createCookbook()->id,
			'summary' => Str::random(100),
			'nutritional_detail' => '{"cal": "462", "carbs": "42g", "protein": "43g", "fat":"28g"}',
			'calorie_count' => 1200,
			'cook_time' => '2020-04-07 00:55:00',
			'servings' => 2
		]);

		$request->setUserResolver(function () {
			return $this->user;
		});

		$recipeStoreRequest = new StoreRequest($request);
		$recipeService = new RecipeService();

		$response = $recipeService->store($recipeStoreRequest->getParams());
		$this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_recipe_instance_when_retrieving_a_recipe_that_exists()
	{
		$recipe = $this->createRecipe();

		$service = new RecipeService();
		$response = $service->show($recipe->id);

		$this->assertInstanceOf(Recipe::class, $response);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_retrieving_a_recipe_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new RecipeService();
		$service->show(0);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_trying_to_update_a_recipe_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new RecipeService();

		$request = new Request([
			'name' => 'new title',
		]);

		$service->update($request, 0);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_200_when_trying_to_update_a_recipe_that_exists()
	{
		$this->createRecipe();

		$service = new RecipeService();

		$request = new Request([
			'name' => 'new title',
			'description' => 'long description'
		]);

		$response = $service->update($request, $this->recipe->id);

		$this->assertSame(Response::HTTP_OK, $response->getStatusCode());
		$this->seeInDatabase('recipes', [
			'id' => $this->recipe->id,
			'name' => 'new title',
			'description' => 'long description'
		]);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_trying_to_delete_a_recipe_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new RecipeService();

		$service->delete(0);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_202_when_trying_to_delete_a_recipe_that_exists()
	{
		$this->createRecipe();

		$service = new RecipeService();

		$response = $service->delete($this->recipe->id);

		$this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
		$this->notSeeInDatabase('recipes', [
			'id' => $this->recipe->id
		]);
	}
}
