<?php

declare(strict_types=1);

namespace Feature;

use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Response;

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
}
