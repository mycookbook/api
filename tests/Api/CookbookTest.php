<?php

namespace Api;

use App\Models\Cookbook;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * Class UserTest
 */
class CookbookTest extends \TestCase
{
    protected string $bookcoverImageUrl =
        "https://www.glamox.com/public/images/image-default.png?scale=canvas&width=640&height=480";

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->artisan('db:seed');
    }

    /**
     * @test
     */
    public function it_can_retrieve_all_cookbooks_and_respond_with_a_200_status_code()
    {
        $this->json('GET', '/api/v1/cookbooks')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'name',
                        'description',
                        'bookCoverImg',
                        'created_at',
                        'updated_at',
                        'slug',
                        'category_id',
                        'flag_id',
                        'resource_type',
                        'is_locked',
                        'alt_text',
                        'tags',
                        '_links',
                        'recipes_count',
                        'categories',
                        'author'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_responds_with_a_404_when_retrieving_a_cookbook_that_does_not_exist()
    {
        $this->json('GET', '/api/v1/cookbooks/0')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertExactJson([
                "error" => "Record Not found."
            ]);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_cookbook_by_id()
    {
        $cookbook = Cookbook::factory()->make();

        $this->json('GET', '/api/v1/cookbooks/' . $cookbook->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'name',
                        'description',
                        'bookCoverImg',
                        'created_at',
                        'updated_at',
                        'slug',
                        'category_id',
                        'flag_id',
                        'resource_type',
                        'is_locked',
                        'alt_text',
                        'tags',
                        '_links',
                        'recipes_count',
                        'categories',
                        'author'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_cookbook_by_slug()
    {
        $cookbook = Cookbook::factory()->make();

        $this->json('GET', '/api/v1/cookbooks/' . $cookbook->slug)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'name',
                        'description',
                        'bookCoverImg',
                        'created_at',
                        'updated_at',
                        'slug',
                        'category_id',
                        'flag_id',
                        'resource_type',
                        'is_locked',
                        'alt_text',
                        'tags',
                        '_links',
                        'recipes_count',
                        'categories',
                        'author'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_if_the_user_is_authorized_to_view_their_cookbooks()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);

        $this->json('GET', '/api/v1/my/cookbooks', [], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ])->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_a_user_with_valid_token_to_create_a_cookbook_resource()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@exampler.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@exampler.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);

        $this->json('POST', '/api/v1/cookbooks', [
            'name' => 'test cookbook',
            'description' => fake()->sentence(150),
            'bookCoverImg' => $this->bookcoverImageUrl,
            'category_id' => 1,
            'categories' => 'keto,vegan,test',
            'flag_id' => 'ng',
            'slug' => 'test-cookbook'
        ], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $this->assertDatabaseHas('cookbooks', [
            'name' => 'test cookbook',
            'slug' => 'test-cookbook'
        ]);
    }

    /**
     * @test
     */
    public function it_allows_a_user_with_valid_token_to_update_own_cookbook_resource()
    {
        $this->markTestIncomplete();
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'update@test.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'update@test.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);

        //create a cookbook
        $this->json('POST', '/api/v1/cookbooks', [
            'name' => 'test cookbook',
            'description' => fake()->sentence(150),
            'bookCoverImg' => $this->bookcoverImageUrl,
            'categories' => 'ketogenic,vegan',
            'flag_id' => "ng",
            'slug' => 'test-cookbook',
            'alt_text' => 'this is a test cookbook'
        ], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $cookbookId = Cookbook::all()->last()->getKey();

        $expected = [
            'name' => 'updated title',
            "alt_text" => "this is an updated alt text",
            'description' => fake()->sentence(150),
            'bookCoverImg' => $this->bookcoverImageUrl,
            'categories' => 'ketogenic',
            'tags' => 'updated tag1, updated tag2'
        ];

        //update the cookbook
        $updateResponse = $this->json('POST', '/api/v1/cookbooks/' . $cookbookId . '/edit', $expected, [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $decoded = json_decode($updateResponse->getContent(), true);
//        dd($decoded);

        $this->assertArrayHasKey("updated", $decoded);
        $this->assertTrue($decoded["updated"]);

        //assertions
        unset($expected["categories"]);
        $expected["tags"] = json_encode($expected["tags"]);
        $this->assertDatabaseHas('cookbooks', $expected);
    }

    /**
     * @test
     */
    public function it_does_not_allow_a_user_with_valid_token_update_a_cookbook_resource_they_dont_own()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);

        $otherUser = User::factory()->make();
        $otherUser->save();
        $otherUser = $otherUser->refresh();

        $cookbook = Cookbook::factory()->make([
            'user_id' => $otherUser->id,
            'bookCoverImg' => $this->bookcoverImageUrl,
        ]);

        $cookbook->save();
        $cookbook = $cookbook->refresh();

        //update the cookbook
        $response = $this->json('POST', '/api/v1/cookbooks/' . $cookbook->id . '/edit', [
            'name' => 'updated title',
            "alt_text" => "this is an updated alt text"
        ], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("error", $decoded);
        $this->assertSame("You are not authorized to access this resource.", $decoded["error"]);
    }

    /**
     * @test
     */
    public function it_forids_lesser_beings_from_deleting_a_cookbook_resource()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);

        //update the cookbook
        $response = $this->json('POST', '/api/v1/cookbooks/1/destroy', [], [
            'HTTP_Authorization' => 'Bearer ' . $decoded['token']
        ]);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("error", $decoded);
        $this->assertSame("You are not authorized to perform this action.", $decoded["error"]);
    }
}
