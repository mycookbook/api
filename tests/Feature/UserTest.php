<?php

declare(strict_types=1);

namespace Feature;

use App\Jobs\SendEmailNotification;
use App\Models\User;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class UserTest extends \TestCase
{
    /**
     * @test
     */
    public function it_returns_422_if_the_request_is_empty()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', []
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that the name field is required
     *
     * @return void
     */
    public function testThatNameFieldIsRequired()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => '',
                'email' => 'sally@foo.com',
                'password' => 'salis',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that the email field is required
     *
     * @return void
     */
    public function testThatEmailFieldIsRequired()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => '',
                'password' => 'salis',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that the email field is valid email
     *
     * @return void
     */
    public function testThatEmailFieldIsValidEmail()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'invalidemailaddress',
                'password' => 'salis',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that the password field is required
     *
     * @return void
     */
    public function testThatPasswordFieldIsRequired()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => '',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that the password field is a minimum of 5 characters
     *
     * @return void
     */
    public function testThatPasswordFieldIsAMinimumOf5Characters()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'sali',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * Test that name, email and password params are required
     *
     * @return void
     */
    public function testThatNameEmailAndPasswordParamsAreRequired()
    {
        Queue::fake();

        $this->json(
            'POST', '/api/v1/auth/register', []
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Queue::assertNotPushed(SendEmailNotification::class);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testAUserCanBeCreated()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        )->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', [
            'name' => 'Joromi',
            'email' => 'joromi@foo.com',
        ]);
    }

    /**
     * Test that a registered users email is required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersEmailIsRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'password' => 'mypassword',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test that a registered users password is required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersPasswordIsRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test /api/users/{1} route
     *
     * @return void
     */
    public function testCanGetOneUser()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'sally',
                'email' => 'sallytu@foo.com',
                'password' => 'salis',
            ]
        );

        $this->json('GET', '/api/v1/users/sallytu@foo.com/verify');

        $response = $this->call('GET', '/api/v1/users/sally');

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    /**
     * @test
     */
    public function it_can_retrieve_all_users()
    {
        $users = User::factory()->count(5)->make();

        $users->map(function ($user) {
            $user->save();
        });

        $this
            ->json('GET', '/api/v1/users')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'name',
                        'cookbooks',
                        'recipes',
                        'contact',
                        'contributions',
                        'email',
                        'following',
                        'followers',
                        'created_at',
                        'updated_at',
                        'name_slug',
                        'pronouns',
                        'avatar',
                        'expertise_level',
                        'about',
                        'can_take_orders',
                        'email_verified'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function it_can_update_user_detail()
    {
        $user = User::factory()->make();
        $user->save();
        $username = $user->refresh()->name_slug;

        $this
            ->json(
                'POST',
                '/api/v1/users/' . $username . '/edit',
                [
                    'pronouns' => 'They/Them/ze'
                ]
            )
            ->assertStatus(200)
            ->assertExactJson([
                'updated' => true,
                'status' => 'success'
            ]);
    }

    /**
     * @test
     */
    public function when_nothing_to_update()
    {
        $user = User::factory()->make();
        $user->save();
        $username = $user->refresh()->name_slug;

        $this
            ->json(
                'POST',
                '/api/v1/users/' . $username . '/edit'
            )
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'nothing to update.'
            ]);
    }

    /**
     * @test
     */
    public function it_can_handle_follow_user()
    {
        $user = User::factory()->make([
            'email' => 'me@test.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $userToFollow = User::factory()->make([
            'email' => 'them@test.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $userToFollow->save();

        $otherUsers = User::factory()->count(5)->make([
            'password' => (new BcryptHasher)->make('pass123'),
        ]);

        $otherUsers->map(function ($user) {
            $user->save();
        });

        $myBearertoken = Auth::attempt([
            'email' => 'me@test.com',
            'password' => 'pass123'
        ]);

        $this->json(
            'POST',
            '/api/v1/follow',
            [
                'toFollow' => $userToFollow->refresh()->getKey()
            ],
            [
                'Authorization' => 'Bearer ' . $myBearertoken
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                [
                    'followers', 'author', 'avatar', 'handle'
                ]
            ]);
    }

    public function test_who_to_folow()
    {
        $user = User::factory()->make([
            'email' => 'me@test.com',
            'password' => (new BcryptHasher)->make('pass123'),
        ]);
        $user->save();

        $myBearertoken = Auth::attempt([
            'email' => 'me@test.com',
            'password' => 'pass123'
        ]);

        $usersToFollow = User::factory()->count(30)->make([
            'password' => (new BcryptHasher)->make('pass123'),
        ]);

        $usersToFollow->map(function ($user) {
            $user->save();
        });

        $this->json(
            'GET',
            '/api/v1/who-to-follow',
            [],
            [
                'Authorization' => 'Bearer ' . $myBearertoken
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                [
                    'followers', 'author', 'avatar', 'handle'
                ]
            ]);
    }
}
