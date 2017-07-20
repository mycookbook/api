<?php


/**
 * Class UserTest
 */
class UserTest extends TestCase
{
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
                'email' => 'sallyleleh@joe.com',
                'password' => 'salitu'
            ]
        )->seeJson(
            [
                'response' => [
                    'created' => true,
                ],
            ]
        );
    }
}
