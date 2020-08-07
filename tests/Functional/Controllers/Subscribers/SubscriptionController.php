<?php

namespace Functional\Controllers\Subscribers;

use App\Subscriber;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SubscriptionController extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_responds_with_a_404_if_the_subscriber_email_is_null()
	{
		$this->json('POST', '/api/v1/subscriptions')
			->seeJson(['email' => ["The email field is required."]])
			->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_subscriber_email_exists_already()
	{
		$subscriber = new Subscriber(['email' => 'obi@yahoo.com']);
		$subscriber->save();

		$this->json('POST', '/api/v1/subscriptions', ['email' => 'obi@yahoo.com'])
			->seeJson(['email' => ["The email has already been taken."]])
			->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_subscriber_email_is_an_invalid_format()
	{
		$this->json('POST', '/api/v1/subscriptions', ['email' => 'invalid-email'])
			->seeJson(['email' => ["The email must be a valid email address."]])
			->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_201_if_the_subscriber_email_is_valid_and_not_taken_already()
	{
		$this->json('POST', '/api/v1/subscriptions', ['email' => 'valid@yahoo.com'])
			->seeJsonStructure(['response' => ['created', 'data']])
			->assertResponseStatus(Response::HTTP_CREATED);
	}
}
