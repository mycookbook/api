<?php

use Tymon\JWTAuth\JWT;

/**
 * Class UserTest
 */
class ApplicationTest extends TestCase
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

        $this->assertEquals(
            'Cookbook API v1.0', $this->response->getContent()
        );
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
     * Test that the email exists already
     *
     * @return void
     */
    public function testThatEmailExists()
    {
        $this->post(
            '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                "email" => [
                    "The email has already been taken."
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
     * Test that name, email and password params are required
     *
     * @return void
     */
    public function testThatNameEmailAndPasswordParamsareRequired()
    {
        $this->json(
            'POST', '/api/v1/signup', []
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
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        )->seeJson(
            [
                'response' => [
                    'created' => true
                ]
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
     * Reset Migrations
     *
     * @return void
     */
    public function tearDown()
    {
        $this->artisan('migrate:reset');
    }

    /**
     * Test that a registered users email is required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersEmailIsRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/signin', [
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
            'POST', '/api/v1/signin', [
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
     * Test that a registered users email and password are required to signin
     *
     * @return void
     */
    public function testThatARegisteredUsersEmailAndPasswordAreRequiredToSignin()
    {
        $this->json(
            'POST', '/api/v1/signin', []
        )->seeJson(
            [
                'password' => [
                    'The password field is required.'
                ],
                'email' => [
                    'The email field is required.'
                ]
            ]
        )->seeStatusCode(422);
    }

    /**
     * Test that a user is signing in with Invalid credentials
     *
     * @return void
     */
    public function testInvalidCredentialsWhenSigningIn()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $this->json(
            'POST', '/api/v1/signin', [
                'email' => 'sally@foo.com',
                'password' => 'invalidpassword'
            ]
        )->seeJson(
            [
                'error' => 'Invalid Credentials.'
            ]
        )->seeStatusCode(401);
    }

    /**
     * Test get all users
     *
     * @return void
     */
    public function testGetAllUsers()
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
     * Test that user cannot be found
     *
     * @return void
     */
    public function testUserNotFound()
    {
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(404, $response->status());

        $this->seeJson(
            [
                'error' => 'Record not found.'
            ]
        );
    }

    /**
     * Test can get all recipes
     *
     * @return void
     */
    public function testCanGetAllRecipes()
    {
        $response = $this->call('GET', '/api/v1/users/1/recipes');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test Recipe can be created
     *
     * @return void
     */
    public function testRecipeCanBeCreated()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/signin', [
            'email' => 'sally@foo.com',
            'password' => 'salis'
            ]
        );

        // TODO: test for UnauthorizedHttpException
        // when Authorization token is not set

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/users/1/cookbook/1/recipes', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbook_id' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'created' => true
            ]
        )->seeStatusCode(201);
    }

    /**
     * Test that recipe name is given
     *
     * @return void
     */
    public function testThatRecipeFieldsAreGiven()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        //        TODO: get Authorization token
        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->post(
            '/api/v1/users/1/cookbook/1/recipes',
            [
                'name' => ' ',
                'ingredients' => ' ',
                'url' => ' ',
                'description' => ' '
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'name' => [
                    'The name field is required.'
                ],
                'ingredients' => [
                    'The ingredients field is required.'
                ],
                'url' => [
                    'The url field is required.'
                ],
                'description' => [
                    'The description field is required.'
                ],
            ]
        );

        $this->assertResponseStatus(422);
    }
}