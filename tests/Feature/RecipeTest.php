<?php

declare(strict_types=1);

namespace Feature;

use App\Models\Cookbook;
use App\Models\Flag;
use App\Models\Recipe;
use App\Models\User;
use Faker\Factory;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                'recipe_id' => rand(1, 10)
            ]
        )->assertStatus(422)
            ->assertExactJson([
                'recipe_id' => [
                    "The selected recipe id is invalid."
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_can_show_my_recipes()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
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

        $response = $this->json(
            'GET',
            '/api/v1/my/recipes',
            [],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200);

        $decoded = json_decode($response->getContent(), true);

        $this->assertCount(3, $decoded['data']);
    }

    /**
     * @test
     */
    public function it_can_report_a_recipe()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
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

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipe->save();

        $this->assertFalse((bool)$recipe->refresh()->is_reported);

        $this->json(
            'POST',
            '/api/v1/report-recipe',
            [
                'recipe_id' => $recipe->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(200)
            ->assertExactJson([
                'message' => 'feedback submitted.'
            ]);

        $this->assertTrue((bool)$recipe->refresh()->is_reported);
    }

    /**
     * @test
     */
    public function it_can_handle_error_reporting_recipe()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        Log::shouldReceive('debug')
            ->once()
            ->with(
                'Error reporting recipe',
                [
                    'message' => 'Invalid recipe id',
                    'recipe_id' => 1
                ]
            );

        $this->withoutExceptionHandling()->json(
            'POST',
            '/api/v1/report-recipe',
            [
                'recipe_id' => 1
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(400)
            ->assertExactJson([
                'message' => 'There was an error processing this request. Please try again later.'
            ]);
    }

    /**
     * @test
     */
    public function it_allows_only_authorized_user_to_report_a_recipe()
    {
        $this->json(
            'POST',
            '/api/v1/report-recipe',
            [
                'recipe_id' => 1
            ],
            [
                'Authorization' => 'Bearer invalid-token'
            ]
        )->assertStatus(401)
            ->assertExactJson([
                'error' => 'Your session has expired. Please login and try again.'
            ]);
    }

    /**
     * @test
     */
    public function only_supers_can_destroy_a_recipe()
    {
        $this->createRoles();

        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $this->createUserRole($user->refresh()->getKey(), 'super');

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipe->save();

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->refresh()->getKey()
        ]);

        $this->json(
            'POST',
            '/api/v1/recipes/' . $recipe->refresh()->getKey() . '/destroy',
            [
                'recipe_id' => $recipe->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(Response::HTTP_ACCEPTED)
            ->assertExactJson([
                "deleted" => true
            ]);
    }

    /**
     * @test
     */
    public function if_you_are_not_a_super_you_cannot_destroy_a_recipe()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipe->save();

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->refresh()->getKey()
        ]);

        $res = $this->json(
            'POST',
            '/api/v1/recipes/' . $recipe->refresh()->getKey() . '/destroy',
            [
                'recipe_id' => $recipe->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                "error" => 'You are not authorized to perform this action.'
            ]);
    }

    /**
     * @test
     */
    public function it_can_update_an_existing_recipe()
    {
        $faker = Factory::create();

        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $cookbook->save();

        $newCookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $newCookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->getKey()
        ]);

        $recipe->save();

        $oldValues = [
            'cookbook_id' => $recipe->refresh()->cookbook_id,
            'description' => $recipe->refresh()->description,
            'summary' => $recipe->refresh()->summary,
            'imgUrl' => $recipe->refresh()->imgUrl,
        ];

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->refresh()->getKey()
        ]);

        $this->json(
            'POST',
            '/api/v1/recipes/' . $recipe->refresh()->getKey() . '/edit',
            [
                'cookbook_id' => $newCookbook->refresh()->getKey(),
                'description' => implode(" ", $faker->words(150)),
                'summary' => implode(" ", $faker->words(55)),
                'imgUrl' => $faker->imageUrl(),
                'ingredients' => [
                    [
                        'name' => $faker->jobTitle,
                        'unit' => '2',
                        'thumbnail' => $faker->imageUrl(),
                    ]
                ],
                'tags' => []
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                "updated" => true
            ]);

        $updatedRecipe = $recipe->refresh();
        $this->assertNotSame($oldValues['cookbook_id'], $updatedRecipe->cookbook_id);
        $this->assertNotSame($oldValues['description'], $updatedRecipe->description);
        $this->assertNotSame($oldValues['summary'], $updatedRecipe->summary);
        $this->assertNotSame($oldValues['imgUrl'], $updatedRecipe->imgUrl);
    }

    /**
     * @test
     */
    public function you_cannot_update_a_recipe_you_do_not_own()
    {
        $faker = Factory::create();

        $me = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $me->save();

        $theOtherUser = User::factory()->make([
            'email' => 'evan.reid2@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $theOtherUser->save();

        $myToken = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $me->refresh()->getKey()
        ]);

        $cookbook->save();

        $salisRecipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $theOtherUser->refresh()->getKey()
        ]);

        $salisRecipe->save();

        $this->json(
            'POST',
            '/api/v1/recipes/' . $salisRecipe->refresh()->getKey() . '/edit',
            [
                'description' => implode(" ", $faker->words(150)),
            ],
            [
                'Authorization' => 'Bearer ' . $myToken
            ]
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_can_create_a_new_recipe()
    {
        $faker = Factory::create();

        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $cookbook->save();

        $flag = new Flag([
            'flag' => 'ng',
            'nationality' => 'nigerien',
        ]);
        $flag->save();

        $this->json(
            'POST',
            '/api/v1/recipes/',
            [
                'is_draft' => 'false',
                'name' => $faker->jobTitle,
                'cookbook_id' => $cookbook->refresh()->getKey(),
                'description' => implode(" ", $faker->words(150)),
                'summary' => implode(" ", $faker->words(55)),
                'imgUrl' => $faker->imageUrl(),
                'ingredients' => [
                    [
                        'name' => $faker->jobTitle,
                        'unit' => '2',
                        'thumbnail' => $faker->imageUrl(),
                    ]
                ],
                'nationality' => $flag->refresh()->flag,
                'cuisine' => 'spanich',
                'tags' => []
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(Response::HTTP_CREATED)
            ->assertExactJson([
                "created" => true
            ]);
    }

    /**
     * @test
     */
    public function handles_invalid_payload_when_updating_existing_cookbook()
    {
        $user = User::factory()->make([
            'email' => 'evan.reid@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $token = Auth::attempt([
            'email' => 'evan.reid@123.com',
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $user->refresh()->getKey()
        ]);

        $cookbook->save();

        $flag = new Flag([
            'flag' => 'ng',
            'nationality' => 'nigerien',
        ]);
        $flag->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $user->refresh()->getKey()
        ]);

        $recipe->save();

        $this->json(
            'POST',
            '/api/v1/recipes/' . $recipe->refresh()->getKey() . '/edit',
            [
                'cookbook_id' => rand(100,105),
                'nationality' => 'fake',
                'imgUrl' => 'not-a-valid-image-url',
                'description' => 'less than 100',
                'summary' => 'less than 50',
                'ingredients' => [],
                'cuisine' => 'spanich',
                'tags' => 'not a list'
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
