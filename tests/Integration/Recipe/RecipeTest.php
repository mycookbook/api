<?php

use Laravel\Lumen\Testing\DatabaseMigrations as DatabaseMigrations;
/**
 * Class UserTest
 */
class RecipeTest extends TestCase
{
    use DatabaseMigrations;

    public function seedTable()
    {
        factory('App\Flag')->create();
        factory('App\Category')->create();
        factory('App\Cookbook')->create();
        factory('App\Recipe')->create();
    }

    /**
     * Test to find cookbook by id
     *
     * @return void
     */
    public function testCanFindRecipe()
    {
        $this->seedTable();
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

        $id = 1;

        $this->get(
            '/api/v1/recipes/' . $id,
            [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'name',
                'ingredients',
                'imgUrl',
                'description',
                'user_id',
                'cookbook_id',
                'created_at',
                'updated_at',
                '_links' => [
                    'self'
                ],
                'user',
                'cookbook',
                'slug',
            ]
        )->assertResponseStatus(200);
    }

    /**
     * Test to find cookbook by id
     *
     * @return void
     */
    public function testCannotFindRecipeIfNotExist()
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

        $id = 0;

        $this->get(
            '/api/v1/recipes/' . $id,
            [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'error'
            ]
        )->assertResponseStatus(404);
    }

    /**
     * Test Recipe can be created
     *
     * @return void
     */
    public function testRecipeCanBeCreated()
    {
        $this->seedTable();
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
            'POST', '/api/v1/recipes', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 1,
                'summary' => 'Cook pasta per package directions. Reserve 3/4 cup cooking liquid, then drain.
                Meanwhile, heat oil in a large, deep skillet on medium. Cook shallot,',
                'nutritional_detail' => 'low carbs'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeStatusCode(201);
    }

    /**
     * Test that Recipe cannot be created with invalid token
     *
     * @return void
     */
    public function testRecipeCannotBeCreatedWithInvalidToken()
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

        $token = 'invalidToken';

        $this->json(
            'POST', '/api/v1/recipes', [
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
                'message' => 'Token is invalid',
                'status' => 'error'
            ]
        )->seeStatusCode(401);
    }

    /**
     * Test cannot process when cookbookId does not exist
     *
     * @return void
     */
    public function testRecipeCannotBeCreatedWhenCookbookIdDoesNotExist()
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
            'POST', '/api/v1/recipes', [
                'name' => 'sample recipe',
                'ingredients' => 'sample1, sample2, sample3',
                'url' => 'http://imagurl.com',
                'description' => 'sample description',
                'user_id' => 1,
                'cookbookId' => 100,
                'summary' => 'Cook pasta per package directions. Reserve 3/4 cup cooking liquid, then drain.
                Meanwhile, heat oil in a large, deep skillet on medium. Cook shallot, ',
                'nutritional_detail' => 'low carbs'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'data',
                'status',
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
        $this->seedTable();

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
            'PUT', '/api/v1/recipes/1', [
                'name' => 'update recipe'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'updated', 'status'
            ]
        )->seejson(
            [
                'updated' => true,
                'status' => 'success'
            ]
        );

        $this->assertResponseStatus(202);
    }

    /**
     * Test that Recipe can not be updated if not found
     *
     * @return void
     */
    public function testRecipeCannotBeUpdatedIfNotExist()
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
            'POST', '/api/v1/recipes', [
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
            'PUT', '/api/v1/recipes/200000', [
                'name' => 'updated recipe name'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'updated',
                'status' => [
                    'error'
                ]
            ]
        );

        $this->assertResponseStatus(404);
    }

    /**
     * Test that recipe name is given
     *
     * @return void
     */
    public function testThatRecipeFieldsAreGiven()
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
            '/api/v1/recipes',
            [
                'name' => ' ',
                'ingredients' => ' ',
                'url' => ' ',
                'description' => ' ',
                'summary' => 'This is not up to 100 characters.'
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
                'summary' => [
                    'The summary must be at least 100 characters.'
                ],
                'nutritional_detail' => [
                    'The nutritional detail field is required.'
                ]
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
            '/api/v1/recipes',
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
    public function testRecipeCannotBeCreatedWhenUserIsNotAuthenticated()
    {
        $this->json(
            'POST', '/api/v1/auth/signup', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $this->post(
            '/api/v1/recipes',
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
     * Test that recipe can be dleted if exist
     *
     * @return void
     */
    public function testThatRecipeCanBeDeleted()
    {
        $this->seedTable();
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

        $recipeId = 1;

        $this->delete(
            '/api/v1/recipes/' . $recipeId,
            [
                'name' => 'test',
                'description' => 'sample'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJson(
            [
                'deleted' => true,
                'status' => 'success'

            ]
        );

        $this->assertResponseStatus(202);
    }

    /**
     * Test that recipe cannot be dleted if not exist
     *
     * @return void
     */
    public function testThatRecipeCannotBeDeletedIfNotExist()
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

        $recipeId = 100000000;

        $this->delete(
            '/api/v1/recipes/' . $recipeId,
            [
                'name' => 'test',
                'description' => 'sample'
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure(
            [
                'deleted',
                'status' => [
                    'error'
                ]
            ]
        );

        $this->assertResponseStatus(404);
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
