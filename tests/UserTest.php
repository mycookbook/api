<?php

/**
 * Class UserTest
 */
class UserTest extends TestCase
{
    /**
     * Run migrations
     * Seed DB
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    /**
     * Test Application
     *
     * @return void
     */
    public function testApplication()
    {
        $response = $this->call('GET', '/api/v1');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test that the name field is required
     *
     * @return void
     */
    public function testThatNameFieldIsRequired()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => '',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                'name' => [
                    'The name field is required.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * Test that the email field is required
     *
     * @return void
     */
    public function testThatEmailFieldIsRequired()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => '',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                'email' => [
                    'The email field is required.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * Test that the email field is valid email
     *
     * @return void
     */
    public function testThatEmailFieldIsValidEmail()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'invalidemailaddress',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                'email' => [
                    'The email must be a valid email address.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * Test that the password field is required
     *
     * @return void
     */
    public function testThatPasswordFieldIsRequired()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => ''
            ]
        )->seeJson(
            [
                'password' => [
                    'The password field is required.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * Test that the password field is a minimum of 5 characters
     *
     * @return void
     */
    public function testThatPasswordFieldIsAMinimumOf5Characters()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'sali'
            ]
        )->seeJson(
            [
                'password' => [
                    'The password must be at least 5 characters.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testAUserCanBeCreated()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                'response' => [
                    'created' => true
                ],
            ]
        )->seeStatusCode(201)->seeInDatabase(
            'users', [
                'name' => 'Sally',
                'email' => 'sally@foo.com'
            ]
        );
    }

    /**
     * Test /api/users route
     *
     * @return void
     */
    public function testCanGetAllUsers()
    {
        $response = $this->call('GET', '/api/v1/users');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test /api/users/{1} route
     *
     * @return void
     */
    public function testCanGetOneUser()
    {
        $response = $this->call('GET', '/api/v1/users/1');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Reset Migrations
     *
     * @return void
     */
    public function tearDown()
    {
        $this->artisan('migrate:reset');
    }
}
