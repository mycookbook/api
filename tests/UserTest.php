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
     * A basic functional test example.
     *
     * @return void
     */
    public function testThatInputIsValid()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => '',
                'email' => 'invalidemailaddress',
                'password' => 'sali'
            ]
        )->seeJson(
            [
                'email' => [
                    'The email must be a valid email address.'
                ],
                'password' => [
                    'The password must be at least 5 characters.'
                ],
                'name' => [
                    'The name field is required.'
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
