<?php

declare(strict_types=1);

namespace Feature;

use Illuminate\Http\Response;

class HttpExceptionsTest extends \TestCase
{
    /**
     * @test
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
}
