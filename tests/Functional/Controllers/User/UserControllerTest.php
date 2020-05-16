<?php

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
/**
 * Class UserControllerTest
 */
class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_returns_422_if_the_request_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/signup', []
		)->seeJson(
			[
				'name' => [
					'The name field is required.'
				],
				'email' => [
					'The email field is required.'
				],
				'password' => [
					'The password field is required.'
				],
			]
		)->seeStatusCode(422);
	}

    /**
     * Test that the name field is required
     *
     * @return void
     */
    public function testThatNameFieldIsRequired()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
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
            'POST', '/api/v1/auth/signup', [
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
            'POST', '/api/v1/auth/signup', [
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
            'POST', '/api/v1/auth/signup', [
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
            'POST', '/api/v1/auth/signup', [
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
     * Test that name, email and password params are required
     *
     * @return void
     */
    public function testThatNameEmailAndPasswordParamsareRequired()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', []
        )->seeJson(
            [
                'name' => [
                    'The name field is required.'
                ],
                'email' => [
                    'The email field is required.'
                ],
                'password' => [
                    'The password field is required.'
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
            'POST', '/api/v1/auth/signup', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        )->seeJsonStructure(
            [
                'response' => [
                    'created',
                    'data' => [
                        '_links' => [
                            'self'
                        ],
                        'created_at',
                        'updated_at',
                        'email',
                        'followers',
                        'following',
                        'name',
                    ],
                    'status'
                ]
            ]
        )->seeStatusCode(201)->seeInDatabase(
            'users', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com'
            ]
        );
    }

    /**
     * Test that user can be uodated
     * This test is for PUT and PATCH operations
     *
     * @return void
     */
    public function testUserCanBeUpdatedIfSignedIn()
    {
        // create the user and sign them in
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );


        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};
        $username = $obj->{'username'};

        $this->put(
            '/api/v1/users/' . $username,
            [
                'name' => 'Joromi 2',
                'follower' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seejson(
            [
                'updated' => true,
                'status' => 'success'
            ]
        );

        $this->assertResponseStatus(Response::HTTP_OK);
    }

    /**
     * Test that user can be uodated
     * This test is for PUT and PATCH operations
     *
     * @return void
     */
    public function testUserCannotBeUpdatedIfNotFound()
    {
        // create the user and sign them in
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->put(
            '/api/v1/users/0',
            [
                'name' => 'Joromi2',
                'follower' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'error'
            ]
        );

        $this->assertResponseStatus(404);
    }

    /**
     * Test that a malicious user cannot be forced to updaye
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
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Joromi',
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'joromi@foo.com',
                'password' => 'joromo1236'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->put(
            '/api/v1/users/0',
            [
                'name' => 'Joromi2',
                'follower' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'error'
            ]
        );

        $this->assertResponseStatus(404);
    }

    /**
     * Test that a registered users email is required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersEmailIsRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/auth/signin', [
                'password' => 'mypassword'
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
     * Test that a registered users password is required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersPasswordIsRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com'
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
     * Test /api/users/{1} route
     *
     * @return void
     */
    public function testCanGetOneUser()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $response = $this->call('GET', '/api/v1/users/sally');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test that user cannot be found
     *
     * @return void
     */
    public function testUserNotFound()
    {
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(404, $response->status());

        $this->seeJsonStructure(
            [
                'error'
            ]
        );
    }
}