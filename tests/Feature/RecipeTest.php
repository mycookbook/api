<?php

declare(strict_types=1);

namespace Feature;

use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RecipeTest extends \TestCase
{
    /**
     * @test
     * todo: service->validatePayload
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
        $recipe->save();

        $this->json('GET', '/api/v1/recipes/' . $recipe->refresh()->slug)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('recipes', [
                "user_id" => $user->id,
                "cookbook_id" => $cookbook->id
            ]
        );
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
    public function it_can_increment_recipe_claps()
    {
        $user = User::factory()->make();

        $user->save();

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipe->save();

        $this->assertTrue($recipe->refresh()->claps == 0);

        $this->json(
            'POST',
            '/api/v1/add-clap',
            [
                'recipe_id' => $recipe->refresh()->getKey()
            ]
        )->assertStatus(200)
            ->assertExactJson([
                'updated' => true,
                'claps' => 1
            ]);

        $this->assertTrue($recipe->refresh()->claps == 1);
    }

    /**
     * @test
     */
    public function it_cannot_clap_for_a_recipe_that_does_not_exist()
    {
        $this->json(
            'POST',
            '/api/v1/add-clap',
            [
                'recipe_id' => rand(1,10)
            ]
        )->assertStatus(422)
        ->assertExactJson([
            'recipe_id' => [
                "The selected recipe id is invalid."
            ]
        ]);
    }

    public function it_can_show_my_recipes()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->getKey()
        ]);

        $cookbook->save();

        $recipes = Recipe::factory()->count(3)->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipes->map(function ($recipe) {
            $recipe->save();
        });

        $this->json(
            'GET',
            '/api/v1/my/recipes',
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200);
    }
}
