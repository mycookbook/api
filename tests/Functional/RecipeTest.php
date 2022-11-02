<?php

namespace Functional;

use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Class UserTest
 */
class RecipeTest extends \TestCase
{
    /**
     * @test
     */
    public function it_can_retrieve_all_recipes_and_respond_with_a_200_status_code()
    {
        $this->json('GET', '/api/v1/recipes')
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_responds_with_a_404_when_retrieving_a_recipe_that_does_not_exist()
    {
        $this->json('GET', '/api/v1/recipes/0')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_recipe_by_id()
    {
        $user = User::factory()->make();

        $user->save();
        $user = $user->refresh();

        $cookbook = Cookbook::factory()->make([
            "user_id" => $user->id
        ]);

        $cookbook->save();
        $cookbook = $cookbook->refresh();

        $recipe = Recipe::factory()->make([
            "user_id" => $user->id,
            "cookbook_id" => $cookbook->id
        ]);

        $this->json('GET', '/api/v1/recipes/' . $recipe->id)
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_recipe_by_slug()
    {
        $user = User::factory()->make();

        $user->save();
        $user = $user->refresh();

        $cookbook = Cookbook::factory()->make([
            "user_id" => $user->id
        ]);

        $cookbook->save();
        $cookbook = $cookbook->refresh();

        $recipe = Recipe::factory()->make([
            "user_id" => $user->id,
            "cookbook_id" => $cookbook->id
        ]);

        $this->json('GET', '/api/v1/recipes/' . $recipe->slug)
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_rejects_the_request_without_access_token()
    {
        $recipePayload = [];

        $response = $this->json('POST', '/api/v1/recipes/', $recipePayload);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $decoded);
        $this->assertSame("Your session has expired. Please login and try again.", $decoded["error"]);
    }

    /**
     * @test
     */
    public function it_rejects_a_request_with_invalid_access_token()
    {
        $this->markTestSkipped();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $userResponse = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($userResponse->getContent(), true);

        $this->json('POST', '/api/v1/cookbooks', [
            'name' => 'test cookbook',
            'description' => fake()->sentence(150),
            'bookCoverImg' => 'https://www.glamox.com/public/images/image-default.png?scale=canvas&width=640&height=480',
            'user_id' => 1,
            'category_id' => 1,
            'categories' => [1],
            'flag_id' => 1,
            'slug' => 'test-cookbook',
            'alt_text' => 'this is a test cookbook'
        ], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $recipePayload = [
            'name' => 'Test Recipe',
            'imgUrl' => 'https://www.glamox.com/public/images/image-default.png?scale=canvas&width=640&height=480',
            'ingredients' => 'rice, meat, water',
            'description' => 'Some description',
            'cookbook_id' => 1,
            'summary' => 'the summary',
            'nutritional_detail' => 'nut deet'
        ];

        $response = $this->json('POST', '/api/v1/recipes/', $recipePayload, ['HTTP_Authorization' => 'Bearer InvalidToken']);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $decoded);
        $this->assertSame("You are not authorized to perform this action.", $decoded["error"]);
    }

    /**
     * @test
     */
    public function it_successfully_creates_a_recipe_resource_when_the_request_contains_a_valid_access_token()
    {
        $this->markTestSkipped();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $userResponse = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($userResponse->getContent(), true);

        $cb = $this->json('POST', '/api/v1/cookbooks', [
            'name' => 'test cookbook',
            'description' => fake()->sentence(150),
            'bookCoverImg' => 'https://www.glamox.com/public/images/image-default.png?scale=canvas&width=640&height=480',
            'user_id' => 1,
            'category_id' => 1,
            'categories' => [1],
            'flag_id' => 1,
            'slug' => 'test-cookbook',
            'alt_text' => 'this is a test cookbook'
        ], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $recipePayload = [
            'name' => 'Test Recipe',
            'imgUrl' => 'https://www.glamox.com/public/images/image-default.png?scale=canvas&width=640&height=480',
            'ingredients' => 'rice, meat, water',
            'description' => 'Some description',
            'cookbook_id' => 1,
            'summary' => 'the summary',
            'nutritional_detail' => 'nut deet'
        ];

        $response = $this->json('POST', '/api/v1/recipes/', $recipePayload, ['HTTP_Authorization' => 'Bearer ' . $decoded["token"]]);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('created', $decoded);
        $this->assertTrue($decoded["created"]);
    }
}
