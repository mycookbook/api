<?php

namespace Tests\Functional\Controllers\Cookbook;

use App\Cookbook;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

/**
 * Class UserControllerTest
 */
class CookbookControllerTest extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_can_retrieve_all_cookbooks_and_respond_with_a_200_status_code()
	{
		$this->json('GET', '/api/v1/cookbooks')
			->seeJsonStructure(['data'])
			->assertResponseStatus(Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_retrieving_a_cookbook_that_does_not_exist()
	{
		$this->json('GET', '/api/v1/cookbooks/0')
			->seeJson(['error' => "Record Not found."])
			->assertResponseStatus(Response::HTTP_NOT_FOUND);
	}

    /**
     * @test
     */
    public function it_can_create_a_cookbook_for_an_authenticated_user()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->json(
            'POST', '/api/v1/cookbooks', [
                'name' => 'sample cookbook',
                'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
                'bookCoverImg' => 'https://cover-image-url',
                'categories' => json_encode([$this->createCategory()->id]),
                'flag_id' => $this->createFlag()->id
            ], [
                'HTTP_Authorization' => 'Bearer' . $token
            ]
        )->seeJsonStructure([
        	'response' => [
        		'created', 'data'
			]
		])->seeStatusCode(Response::HTTP_CREATED);
    }

	/**
	 * @test
	 */
    public function it_strips_out_duplicate_categories_before_creating_a_cookbook_for_an_authenticated_user()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$category1 = $this->createCategory()->id;
		$category2 = $this->createCategory()->id;

		$this->json(
			'POST', '/api/v1/cookbooks', [
			'name' => 'sample cookbook',
			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
			'bookCoverImg' => 'https://cover-image-url',
			'categories' => json_encode([$category1, $category2, $category2]),
			'flag_id' => $this->createFlag()->id
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJsonStructure([
			'response' => [
				'created', 'data'
			]
		])->seeStatusCode(Response::HTTP_CREATED);

		$this->assertCount(2, Cookbook::all()->last()->categories()->get());
	}

	/**
	 * @test
	 */
	public function it_cannot_create_a_cookbook_for_an_unauthenticated_user()
	{
		$this->json(
			'POST', '/api/v1/cookbooks', [
			'name' => 'sample cookbook',
			'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
			'bookCoverImg' => 'https://cover-image-url',
			'categories' => json_encode([$this->createCategory()->id]),
			'flag_id' => $this->createFlag()->id
		], [
				'HTTP_Authorization' => 'Bearer' . 'invalid_token'
			]
		)->seeJson([
			'status' => "error",
			'message' => "Token is invalid"
		])->seeStatusCode(401);
	}

    /**
	 * @test
     */
    public function it_responds_with_a_422_if_the_request_is_empty()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        
        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->post(
            '/api/v1/cookbooks',
            [],
			['HTTP_Authorization' => 'Bearer' . $token]
        )->seeJson(
            [
                'name' => [
                    'The name field is required.'
                ],
                'description' => [
                    'The description field is required.'
                ],
                'bookCoverImg' => [
                  'The book cover img field is required.'
                ]
            ]
        );

        $this->assertResponseStatus(422);
    }

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_name_field_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => '',
				'description' => 'sample description',
				'bookCoverImg' => 'http://sample-image'
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'name' => [
					'The name field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_name_field_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'description' => 'sample description',
				'bookCoverImg' => 'http://sample-image'
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'name' => [
					'The name field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_description_field_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => '',
				'bookCoverImg' => 'http://sample-image'
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'description' => [
					'The description field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_description_field_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'bookCoverImg' => 'http://sample-image'
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'description' => [
					'The description field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_description_field_is_less_than_126_characters()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => 'short description',
				'bookCoverImg' => 'http://sample-image'
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'description' => [
					'The description must be at least 126 characters.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_bookCoverImg_field_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => '',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => 1
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'bookCoverImg' => [
					'The book cover img field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_bookCoverImg_field_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => 1
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'bookCoverImg' => [
					'The book cover img field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_bookCoverImg_field_is_not_a_valid_url()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'invalid-url',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => 1
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'bookCoverImg' => [
					'The book cover img format is invalid.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_category_id_field_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'categories' => '',
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'categories' => [
					'The categories field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_category_id_field_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'categories' => [
					'The categories field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_category_id_does_not_exist_in_the_db()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);
		
		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'categories' => json_encode([0]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'error' => 'Category does not exist'
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_flag_id_field_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);


		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => ''
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'flag_id' => [
					'The flag id field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_flag_id_field_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'categories' => json_encode([$this->createCategory()->id]),
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'flag_id' => [
					'The flag id field is required.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_flag_id_does_not_exist_in_the_db()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->post(
			'/api/v1/cookbooks',
			[
				'name' => 'sample title',
				'description' => Str::random(126),
				'bookCoverImg' => 'http://sample-image',
				'categories' => json_encode([$this->createCategory()->id]),
				'flag_id' => 0
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'flag_id' => [
					'The selected flag id is invalid.'
				]
			]
		);

		$this->assertResponseStatus(422);
	}

	/**
	 * @test
	 */
	public function it_can_update_an_existing_cookbook_for_an_authenticated_user()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$cookbook = $this->createCookbook();

		$this->json(
			'PUT', '/api/v1/cookbooks' . '/' . $cookbook->id, [
			'name' => 'new title'
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJsonStructure([
			'updated'
		])->seeStatusCode(Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_an_authenticated_user_tries_to_update_a_cookbook_that_does_not_exist()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'PUT', '/api/v1/cookbooks/0', [
			'name' => 'new title'
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson([
			'error' => 'Record Not found.'
		])->seeStatusCode(Response::HTTP_NOT_FOUND);
	}

	/**
	 * @test
	 */
	public function it_cannot_update_a_cookbook_for_a_user_with_an_invalid_token()
	{
		$cookbook = $this->createCookbook();
		$this->json(
			'PUT', '/api/v1/cookbooks' . '/' . $cookbook->id, [
			'name' => 'new title'
		], [
				'HTTP_Authorization' => 'Bearer' . 'invalid-token'
			]
		)->seeJson([
			'status' => "error",
			'message' => "Token is invalid"
		])->seeStatusCode(401);
	}

	/**
	 * @test
	 */
	public function it_can_delete_an_existing_cookbook_for_an_authenticated_user()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$cookbook = $this->createCookbook();

		$this->json(
			'DELETE', '/api/v1/cookbooks' . '/' . $cookbook->id,
			[],
			['HTTP_Authorization' => 'Bearer' . $token]
		)->seeJsonStructure([
			'deleted'
		])->seeStatusCode(Response::HTTP_ACCEPTED);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_an_authenticated_user_tries_to_delete_a_cookbook_that_does_not_exist()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$this->json(
			'DELETE', '/api/v1/cookbooks/0',
			[],
			['HTTP_Authorization' => 'Bearer' . $token]
		)->seeJson([
			'error' => 'Record Not found.'
		])->seeStatusCode(Response::HTTP_NOT_FOUND);
	}

	/**
	 * @test
	 */
	public function it_cannot_delete_a_cookbook_for_a_user_with_an_invalid_token()
	{
		$cookbook = $this->createCookbook();
		$this->json(
			'DELETE', '/api/v1/cookbooks' . '/' . $cookbook->id,
			[],
			['HTTP_Authorization' => 'Bearer' . 'invalid-token']
		)->seeJson([
			'status' => "error",
			'message' => "Token is invalid"
		])->seeStatusCode(401);
	}
}
