<?php

namespace Functional\Exceptions;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Response;

/**
 * Class HttpExceptionsTest
 */
class HttpExceptionsTest extends \TestCase
{
    use WithoutMiddleware;

    /**
     * Test testNotFoundHttpException
     *
     * @return void
     */
    public function testNotFoundHttpException()
    {
        $response = $this->call('GET', '/api/v1/notfound');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }

    /**
     * @test
     */
    public function it_responds_with_a_404_when_trying_to_login_with_invalid_credentials()
    {
        $response = $this->call('POST', '/api/v1/auth/login', ['email' => 'invalid-email', 'password' => 'invalid-password']);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_when_trying_to_login_without_credentials()
    {
        $this->markTestIncomplete();

        $response = $this->call('POST', '/api/v1/auth/login', []);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
    }
}
