<?php

namespace Functional\Controllers;

use App\Jobs\SendEmailNotification;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;

/**
 * Class UserControllerTest
 */
class UserControllerTest extends \TestCase
{
    use WithoutMiddleware;

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
//    	Queue::fake();

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

//        Queue::assertPushed(SendEmail::class);
    }

    /**
     * Test that user can be uodated
     * This test is for PUT and PATCH operations
     *
     * @return void
     */
    public function testUserCanBeUpdatedIfSignedIn()
    {
        $this->markTestIncomplete();

        // create the user and sign them in
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $this->json('GET', '/api/v1/users/joromi@foo.com/verify');

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $obj = json_decode($res->getContent());
        $token = $obj->{'token'};
        $username = $obj->{'username'};

        $this->put(
            '/api/v1/users/'.$username,
            [
                'name' => 'Joromi 2',
                'followers' => 1,
            ], [
                'HTTP_Authorization' => 'Bearer'.$token,
            ]
        );

        $this->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test that user can be uodated
     * This test is for PUT and PATCH operations
     *
     * @return void
     */
    public function testUserCannotBeUpdatedIfNotFound()
    {
        $this->markTestIncomplete();
        // create the user and sign them in
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $this->json('GET', '/api/v1/users/joromi@foo.com/verify');

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $obj = json_decode($res->getContent());
        $token = $obj->{'token'};

        $this->put(
            '/api/v1/users/0',
            [
                'name' => 'Joromi2',
                'follower' => 1,
            ], [
                'HTTP_Authorization' => 'Bearer'.$token,
            ]
        )->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test that a malicious user cannot be forced to update
     * This test is for PUT and PATCH operations
     * use case: a signed in user is trying to perform a funny operation
     * By passing in a user id of another user not signed in
     * Or a user that does not exist in the database
     * This test is not exactly comprehensive
     * It only tests for a user that does not exist
     * It currently does not test for a user that actually exist
     * But does not have a token
     * The test will be reviewed
     *
     * @return void
     */
    public function testMaliciousUserCannotBeUpdated()
    {
        $this->markTestIncomplete();
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $this->json('GET', '/api/v1/users/joromi@foo.com/verify');

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236',
            ]
        );

        $obj = json_decode($res->getContent());
        $token = $obj->{'token'};

        $this->put(
            '/api/v1/users/0',
            [
                'name' => 'Joromi2',
                'followers' => 1,
            ], [
                'HTTP_Authorization' => 'Bearer'.$token,
            ]
        )->assertStatus(Response::HTTP_NOT_FOUND);
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
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
        $content = json_decode($response->getContent());
        $this->assertSame('Record Not found.', $content->error);
    }
}
