<?php

namespace Functional;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;

class SubscriptionTest extends \TestCase
{
    use WithoutMiddleware;

    /**
     * @test
     */
    public function it_responds_with_a_404_if_the_subscriber_email_is_null()
    {
        $this->json('POST', '/api/v1/subscriptions')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_subscriber_email_exists_already()
    {
        $subscriber = new Subscriber(['email' => 'obi@yahoo.com']);
        $subscriber->save();

        $this->json('POST', '/api/v1/subscriptions', ['email' => 'obi@yahoo.com'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_subscriber_email_is_an_invalid_format()
    {
        $this->json('POST', '/api/v1/subscriptions', ['email' => 'invalid-email'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_201_if_the_subscriber_email_is_valid_and_not_taken_already()
    {
        $this->json('POST', '/api/v1/subscriptions', ['email' => 'valid@yahoo.com'])
            ->assertStatus(Response::HTTP_CREATED);
    }
}
