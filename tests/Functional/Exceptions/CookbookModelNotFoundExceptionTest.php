<?php

namespace Functional\Exceptions;

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\WithoutMiddleware;

class CookbookModelNotFoundExceptionTest extends \TestCase
{
    use WithoutMiddleware;

    /**
     * @test
     */
    public function it_responds_with_a_404_when_trying_to_find_a_user_that_does_not_exist()
    {
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }
}
