<?php

namespace Functional;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;

/**
 * Class UserTest
 */
class CookbookTest extends \TestCase
{
    use WithoutMiddleware;

    /**
     * @test
     */
    public function it_can_retrieve_all_cookbooks_and_respond_with_a_200_status_code()
    {
        $this->json('GET', '/api/v1/cookbooks')
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_responds_with_a_404_when_retrieving_a_cookbook_that_does_not_exist()
    {
        $this->json('GET', '/api/v1/cookbooks/0')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_cookbook_by_id()
    {
        $cookbook = $this->createCookbook();

        $this->json('GET', '/api/v1/cookbooks/'.$cookbook->id)
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_when_retrieving_a_cookbook_by_slug()
    {
        $cookbook = $this->createCookbook();

        $this->json('GET', '/api/v1/cookbooks/'.$cookbook->slug)
            ->assertStatus(Response::HTTP_OK);
    }
}
