<?php


/**
 * Class UserTest
 */
class CookbookTest extends TestCase
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
     * Test that Cookbook can be created
     *
     * @return void
     */
    public function testCookbookCanBeCreated()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/cookbooks', [
                'name' => 'sample cookbook',
                'description' => 'sample description'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'created' => true,
                'links' => [
                    'get' => 'api/v1/cookbooks/' . 2,
                    'put' => 'api/v1/cookbooks/' . 2,
                    'patch' => 'api/v1/cookbooks/' . 2,
                    'delete' => 'api/v1/cookbooks/' . 2
                ]
            ]
        )->seeStatusCode(200);
    }

    /**
     * Test that cookbook name is given
     *
     * @return void
     */
    public function testThatCookbookFieldsAreGiven()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        //        TODO: get Authorization token
        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->post(
            '/api/v1/cookbooks',
            [
                'name' => ' ',
                'description' => ' '
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'name' => [
                    'The name field is required.'
                ],
                'description' => [
                    'The description field is required.'
                ],
            ]
        );

        $this->assertResponseStatus(422);
    }

    /**
     * Test can get all the cookbooks for one user
     *
     * @return void
     */
    public function testCanGetAllCookbooksForOneUser()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        // TODO: test for UnauthorizedHttpException
        // when Authorization token is not set

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->get(
            '/api/v1/cookbooks',
            [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        );

        $this->assertResponseStatus(200);
    }

    /**
     * Test Cookbook cannot be created when token is invalid
     *
     * @return void
     */
    public function testCookbookCannotBeCreatedWhenTokenIsInvalid()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        // invalid token
        $token = 'invalidToken';

        $this->post(
            '/api/v1/cookbooks',
            [
                'name' => ' ',
                'description' => ' '
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'status' => 'error',
                'message' => 'Token is invalid'
            ]
        );
    }

    /**
     * Test that cookbook can be updated if found
     *
     * @return void
     */
    public function testThatCookbookCanBeUpdated()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $cookbookId = 1;

        $this->put(
            '/api/v1/cookbooks/' . $cookbookId,
            [
                'name' => 'test',
                'description' => 'sample'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'response' => [
                    'updated' => true,
                    'status' => 204
                ]
            ]
        );

        $this->assertResponseStatus(204);
    }

    /**
     * Test that cookbook can be updated if not found
     *
     * @return void
     */
    public function testThatCookbookCannotBeUpdatedIfNotFound()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $cookbookId = 2;

        $this->put(
            '/api/v1/cookbooks/' . $cookbookId,
            [
                'name' => 'test',
                'description' => 'sample update'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'response' => [
                    'error' => 'Cookbook does not exist.',
                    'status' => 404
                ]
            ]
        );

        $this->assertResponseStatus(404);
    }

    /**
     * Test that cookbook can be dleted if exist
     *
     * @return void
     */
    public function testThatCookbookCanBeDeleted()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/signin', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $cookbookId = 1;

        $this->delete(
            '/api/v1/cookbooks/' . $cookbookId,
            [
                'name' => 'test',
                'description' => 'sample'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'response' => [
                    'deleted' => true,
                    'status' => 202
                ]
            ]
        );

        $this->assertResponseStatus(202);
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
