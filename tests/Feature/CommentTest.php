<?php

declare(strict_types=1);

namespace Feature;

use App\Models\Comment;
use App\Models\Cookbook;
use App\Models\Recipe;
use App\Models\User;
use Faker\Factory;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentTest extends \TestCase
{
    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->make([
            'email' => 'test@123.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $this->user = $user->refresh();
    }

    /**
     * @test
     */
    public function it_can_add_a_comment_if_valid_access_token(): void
    {
        $faker = Factory::create();

        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $this->json(
            'POST',
            '/api/v1/comments',
            [
                'resource-type' => 'recipe',
                'resource-id' => $recipe->refresh()->getKey(),
                'comment' => $faker->sentence
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'created' => true
            ]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_token_is_malformed_or_invalid()
    {
        $this->json(
            'POST',
            '/api/v1/comments',
            [
                'resource-type' => 'recipe',
                'resource-id' => 1,
                'comment' => 'fake sentence'
            ],
            [
                'Authorization' => 'Bearer malformed-or-invalid-token'
            ]
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_handles_error_when_saving_comment()
    {
        $faker = Factory::create();
        $payload = [
            'resource-type' => 'recipe',
            'resource-id' => 'invalid',
            'comment' => $faker->sentence
        ];

        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        Log::shouldReceive('debug')
            ->once()
            ->with(
                'comment creation failed.',
                [
                    'error' => 'No query results for model [App\Models\Recipe] invalid',
                    'payload' => $payload
                ]
            );

        $this->withoutExceptionHandling()->json(
            'POST',
            '/api/v1/comments',
            $payload,
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'error' => 'There was an error processing this request. Please try again later.'
            ]);
    }

    /**
     * @test
     */
    public function it_can_destroy_user_owned_comment()
    {
        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $comment = Comment::factory()->make([
            'user_id' => $this->user->getKey(),
            'recipe_id' => $recipe->refresh()->getKey()
        ]);

        $comment->save();

        $this->json(
            'POST',
            '/api/v1/comments/destroy',
            [
                'comment-id' => $comment->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'deleted' => true
            ]);
    }

    /**
     * @test
     */
    public function it_cannot_destroy_user_not_owned_comment()
    {
        $theOtherUser = User::factory()->make();
        $theOtherUser->save();

        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $comment = Comment::factory()->make([
            'user_id' => $theOtherUser->refresh()->getKey(),
            'recipe_id' => $recipe->refresh()->getKey()
        ]);

        $comment->save();

        $this->json(
            'POST',
            '/api/v1/comments/destroy',
            [
                'comment-id' => $comment->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertExactJson([
            'error' => 'You are not authorized to perform this action.'
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_cannot_add_comment_for_an_unauthorized_user()
    {
        $faker = Factory::create();

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $this->json(
            'POST',
            '/api/v1/comments',
            [
                'resource-type' => 'recipe',
                'resource-id' => $recipe->refresh()->getKey(),
                'comment' => $faker->sentence
            ],
            [
                'Authorization' => 'Bearer unauthorized-access-token'
            ]
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_cannot_destroy_comment_if_access_token_is_malformed_or_invalid()
    {
        $this->json(
            'POST',
            '/api/v1/comments/destroy',
            [
                'comment-id' => 1
            ],
            [
                'Authorization' => 'Bearer malformed-or-invalid-token'
            ]
        )->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     * scnario: user does not own recipe but isSuper
     */
    public function only_supers_can_destroy_a_comment()
    {
        $theOtherUser = User::factory()->make();
        $theOtherUser->save();

        $this->createRoles();
        $this->createUserRole($this->user->refresh()->getKey(), 'super');

        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $comment = Comment::factory()->make([
            'user_id' => $theOtherUser->refresh()->getKey(),
            'recipe_id' => $recipe->refresh()->getKey()
        ]);

        $comment->save();

        $this->json(
            'POST',
            '/api/v1/comments/destroy',
            [
                'comment-id' => $comment->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertExactJson([
            'deleted' => true
        ])->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function handles_when_not_user_and_not_own_comment()
    {
        $theOtherUser = User::factory()->make();
        $theOtherUser->save();

        $this->createRoles();
        $this->createUserRole($this->user->refresh()->getKey(), 'contributor');

        $token = Auth::attempt([
            'email' => $this->user->email,
            'password' => 'pass123'
        ]);

        $cookbook = Cookbook::factory()->make([
            'user_id' => $this->user->getKey()
        ]);

        $cookbook->save();

        $recipe = Recipe::factory()->make([
            'cookbook_id' => $cookbook->refresh()->getKey(),
            'user_id' => $this->user->getKey()
        ]);

        $recipe->save();

        $comment = Comment::factory()->make([
            'user_id' => $theOtherUser->refresh()->getKey(),
            'recipe_id' => $recipe->refresh()->getKey()
        ]);

        $comment->save();

        $this->json(
            'POST',
            '/api/v1/comments/destroy',
            [
                'comment-id' => $comment->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $token
            ]
        )->assertExactJson([
            'error' => 'You are not authorized to perform this action.'
        ])->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
