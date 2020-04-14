<?php

namespace Tests\Integration\Controllers\Cookbook;

/**
 * Class UserTest
 */
class CookbookTest extends \TestCase
{
	/**
     * Test to find cookbook by id
     *
     * @return void
     */
    public function testCanFindCookbook()
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
//        $newUser = $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//        dd($newUser->getActualOutput());
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $cookbookId = 1;
//
//        $this->get(
//            '/api/v1/cookbooks/' . $cookbookId,
//            [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJsonStructure(
//            [
//                'name',
//                'description',
//                'bookCoverImg',
//                'user_id',
//                'created_at',
//                'updated_at',
//                '_links',
//                'slug',
//            ]
//        )->assertResponseStatus(200);
    }

//    /**
//     * Test that Cookbook can be created
//     *
//     * @return void
//     */
//    public function testCookbookCanBeCreated()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $this->json(
//            'POST', '/api/v1/cookbooks', [
//                'name' => 'sample cookbook',
//                'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
//                'bookCoverImg' => 'https://cover-image-url',
//                'category_id' => 1,
//                'flag_id' => 1
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJsonStructure(
//            [
//                'response' => [
//                    'created',
//                    'data',
//                    'status'
//                ]
//            ]
//        )->seeStatusCode(201);
//    }
//
//    /**
//     * Test that cookbook name is given
//     *
//     * @return void
//     */
//    public function testThatCookbookFieldsAreGiven()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        //        TODO: get Authorization token
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $this->post(
//            '/api/v1/cookbooks',
//            [
//                'name' => ' ',
//                'description' => ' ',
//                'bookCoverImg' => ' '
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJson(
//            [
//                'name' => [
//                    'The name field is required.'
//                ],
//                'description' => [
//                    'The description field is required.'
//                ],
//                'bookCoverImg' => [
//                  'The book cover img field is required.'
//                ]
//            ]
//        );
//
//        $this->assertResponseStatus(422);
//    }
//
//    /**
//     * Test can get all the cookbooks for active user
//     *
//     * @return void
//     */
//    public function testCanGetAllCookbooksForActiveUser()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        // TODO: test for UnauthorizedHttpException
//        // when Authorization token is not set
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $this->get(
//            '/api/v1/cookbooks',
//            [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        );
//
//        $this->assertResponseStatus(200);
//    }
//
//    /**
//     * Test can get aone cookbook that exist
//     *
//     * @return void
//     */
//    public function testCanGetOneCookbookIfExist()
//    {
//        $this->seedTable();
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        // TODO: test for UnauthorizedHttpException
//        // when Authorization token is not set
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $id = 1;
//
//        $this->get(
//            '/api/v1/cookbooks/' . $id,
//            [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        );
//
//        $this->assertResponseStatus(200);
//    }
//
//    /**
//     * Test Cookbook cannot be created when token is invalid
//     *
//     * @return void
//     */
//    public function testCookbookCannotBeCreatedWhenTokenIsInvalid()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        // invalid token
//        $token = 'invalidToken';
//
//        $this->post(
//            '/api/v1/cookbooks',
//            [
//                'name' => ' ',
//                'description' => ' ',
//                'bookCoverImg' => ' ',
//                'flag' => ' '
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJson(
//            [
//                'status' => 'error',
//                'message' => 'Token is invalid'
//            ]
//        );
//    }
//
//    /**
//     * Test that cookbook can be updated if found
//     *
//     * @return void
//     */
//    public function testThatCookbookCanBeUpdated()
//    {
//        $this->seedTable();
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $cookbookId = 1;
//
//        $this->put(
//            '/api/v1/cookbooks/' . $cookbookId,
//            [
//                'name' => 'test',
//                'description' => 'sample'
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJsonStructure(
//            [
//               'updated', 'status'
//            ]
//        )->seejson(
//            [
//                'updated' => true,
//                'status' => 'success'
//            ]
//        );
//
//        $this->assertResponseStatus(202);
//    }
//
//    /**
//     * Test that cookbook can be updated if not found
//     *
//     * @return void
//     */
//    public function testThatCookbookCannotBeUpdatedIfNotFound()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $cookbookId = 2000;
//
//        $this->put(
//            '/api/v1/cookbooks/' . $cookbookId,
//            [
//                'name' => 'test',
//                'description' => 'sample update'
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJsonStructure(
//            [
//                'updated',
//                'status' => [
//                    'error'
//                ]
//            ]
//        );
//
//        $this->assertResponseStatus(404);
//    }
//
//    /**
//     * Test that cookbook can be dleted if exist
//     *
//     * @return void
//     */
//    public function testThatCookbookCanBeDeleted()
//    {
//        $this->seedTable();
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $cookbookId = 1;
//
//        $this->delete(
//            '/api/v1/cookbooks/' . $cookbookId,
//            [
//                'name' => 'test',
//                'description' => 'sample'
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJson(
//            [
//                'deleted' => true,
//                'status' => 'success'
//
//            ]
//        );
//
//        $this->assertResponseStatus(202);
//    }
//
//    /**
//     * Test that cookbook can be dleted if not exist
//     *
//     * @return void
//     */
//    public function testThatCookbookCannotBeDeletedIfNotFound()
//    {
//        $this->json(
//            'POST', '/api/v1/auth/signup', [
//                'name' => 'Sally',
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $res = $this->json(
//            'POST', '/api/v1/auth/signin', [
//                'email' => 'sally@foo.com',
//                'password' => 'salis'
//            ]
//        );
//
//        $obj = json_decode($res->response->getContent());
//        $token = $obj->{'token'};
//
//        $cookbookId = 100000;
//
//        $this->delete(
//            '/api/v1/cookbooks/' . $cookbookId,
//            [
//                'name' => 'test',
//                'description' => 'sample'
//            ], [
//                'HTTP_Authorization' => 'Bearer' . $token
//            ]
//        )->seeJsonStructure(
//            [
//                'deleted',
//                'status' => [
//                    'error'
//                ]
//            ]
//        );
//
//        $this->assertResponseStatus(404);
//    }

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
