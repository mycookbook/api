<?php


/**
 * Class UserTest
 */
class RecipeTest extends TestCase
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

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/recipe', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'created' => true,
                'recipeId' => 2
            ]
        )->seeStatusCode(201);
    }

    /**
     * Test cannot process when cookbookId does not exist
     *
     * @return void
     */
    public function testRecipeCannotBeCreatedWhenCookbookIdDoesNotExist()
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

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/recipe', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 100
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'error' => 'Cookbook not found'
            ]
        )->seeStatusCode(404);
    }

    /**
     * Test that Recipe can be updated if found
     *
     * @return void
     */
    public function testRecipeCanBeUpdatedIfExist()
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

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/recipe', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        );

        $this->json(
            'PUT', '/api/v1/recipe/2', [
                'name' => 'update recipe'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'updated' => true
            ]
        )->seeStatusCode(204);
    }

    /**
     * Test that Recipe can be updated if not found
     *
     * @return void
     */
    public function testRecipeCanBeUpdatedIfNotExist()
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

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/recipe', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 1
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        );

        $this->json(
            'PUT', '/api/v1/recipe/200', [
                'name' => 'update recipe'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'error' => 'Recipe does not exist.'
            ]
        )->seeStatusCode(404);
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
            '/api/v1/recipe',
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

    /**
     * Test can get all the recipes for one user
     *
     * @return void
     */
    public function testCanGetAllRecipesForOneUser()
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

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->get(
            '/api/v1/recipes',
            [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        );

        $this->assertResponseStatus(200);
    }

    /**
     * Test Recipe cannot be created when token is invalid
     *
     * @return void
     */
    public function testRecipeCannotBeCreatedWhenTokenIsInvalid()
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
                'password' => 'salis'
            ]
        );

        // invalid token
        $token = 'invalidToken';

        $this->post(
            '/api/v1/recipe',
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
                'status' => 'error',
                'message' => 'Token is invalid'
            ]
        );
    }

    /**
     * Test Recipe cannot be created when user is not authenticated
     *
     * @return void
     */
    public function testRecipeCannotBeCretaedWhenUserIsNotAuthenticated()
    {
        $this->json(
            'POST', '/api/v1/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $this->post(
            '/api/v1/recipe',
            [
                'name' => 'sample',
                'ingredients' => 'sample',
                'url' => 'sample',
                'description' => 'sample'
            ]
        );

        $this->assertResponseStatus(401);
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
