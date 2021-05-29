<?php

namespace Functional\Controllers\Cookbook;

use App\Cookbook;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutMiddleware;

/**
 * Class UserControllerTest
 */
class CookbookControllerTest extends \TestCase
{
	use WithoutMiddleware;
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

        $this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'alt_text' => 'example',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
                'category_id' => json_encode($this->createCategory()->id),
                'flag_id' => $this->createFlag()->id,
				'categories' => json_encode($this->createCategory()->id) . ',' . json_encode($this->createCategory()->id)
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
			'alt_text' => 'example',
			'bookCoverImg' => 'http://lorempixel.com/400/200/',
			'category_id' => $category1,
			'flag_id' => $this->createFlag()->id,
			'categories' => implode(',', [$category1, $category2])
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
	public function it_responds_with_422_if_the_additional_categories_is_more_than_two()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
			'alt_text' => 'example',
			'bookCoverImg' => 'http://lorempixel.com/400/200/',
			'category_id' => $this->createCategory()->id,
			'flag_id' => $this->createFlag()->id,
			'categories' => implode(',', [$this->createCategory()->id, $this->createCategory()->id, $this->createCategory()->id])
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'categories' => [
					'The categories cannot exceed 2.'
				]
			]
		)->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_422_if_the_additional_categories_is_invalid()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
			'alt_text' => 'example',
			'bookCoverImg' => 'http://lorempixel.com/400/200/',
			'category_id' => $this->createCategory()->id,
			'flag_id' => $this->createFlag()->id,
			'categories' => implode(',', [190])
		], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'categories' => [
					'The selected categories is invalid.'
				]
			]
		)->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => 'salis'
            ]
        );

        $obj = json_decode($res->response->getContent());
        $token = $obj->{'token'};

        $this->post(
            '/api/v1/cookbooks', [],
			[
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
                'bookCoverImg' => [
                  'The book cover img field is required.'
                ],
				"category_id" => [
					"The category id field is required."
				],
				"flag_id" => [
					"The flag id field is required."
				]
            ]
        );

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => '',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'short description',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => '',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_if_the_bookCoverImg_field_is_not_a_valid_image_url()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'invalid-url',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'bookCoverImg' => [
					'The book cover img format is not supported'
				]
			]
		);

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => '',
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'category_id' => [
					'The category id field is required.'
				]
			]
		);

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		)->seeJson(
			[
				'category_id' => [
					'The category id field is required.'
				]
			]
		);

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => 0,
				'categories' => implode(',', [$this->createCategory()->id]),
				'flag_id' => $this->createFlag()->id
			], [
				'HTTP_Authorization' => 'Bearer' . $token
			]
		);

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id])
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
				'name' => 'sample cookbook',
				'description' => 'Qui quia vel dolor dolores aut in. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incid idunt.',
				'bookCoverImg' => 'http://lorempixel.com/400/200/',
				'alt_text' => 'example',
				'category_id' => $this->createCategory()->id,
				'categories' => implode(',', [$this->createCategory()->id]),
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

		$this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

		$res = $this->json(
			'POST', '/api/v1/auth/login', [
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$obj = json_decode($res->response->getContent());
		$token = $obj->{'token'};

		$cookbook = $this->createCookbook();

		$response = $this->json(
			'PUT', '/api/v1/cookbooks/' . $cookbook->id, [
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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
	public function it_can_delete_an_existing_cookbook_for_an_authenticated_user()
	{
		$this->json(
			'POST', '/api/v1/auth/register', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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

		$this->json('GET', '/api/v1/users/sally@foo.com/verify');

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
}
