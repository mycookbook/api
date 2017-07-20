<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

/**
 * Class UserTest
 */
class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    /**
     *
     */
    public function testApplication()
    {
        $response = $this->call('GET', '/api');

        $this->assertEquals(200, $response->status());
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->json(
            'POST', '/api/signup', [
                'name' => 'Sally',
                'email' => 'sallyleleh@omosfdffunuu.com',
                'password' => 'salitu'
            ]
        )->seeJson(
            [
                'response' => [
                    'created' => true
                ],
            ]
        );
    }

    public function tearDown()
    {
        $this->artisan('migrate:reset');
    }
}
