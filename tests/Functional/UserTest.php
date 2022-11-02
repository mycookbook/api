<?php

namespace Functional;

use App\Jobs\SendEmailNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;

/**
 * Class UserTest
 */
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
     * Test that user cannot be found
     *
     * @return void
     */
    public function testUserNotFound()
    {
        $this->markTestSkipped();
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
        $content = json_decode($response->getContent());
        $this->assertSame('Record Not found.', $content->error);
    }
}
