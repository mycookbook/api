<?php

namespace Functional;

use App\User;
use Illuminate\Http\Response;

class ExceptionsTest extends \TestCase
{
    /**
     * @test
     */
    public function it_responds_with_a_404_when_trying_to_find_a_user_that_does_not_exist()
    {
        $response = $this->call('GET', '/api/v1/users/0');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
    }

    /**
     * @test
     */
    public function it_responds_with_a_200_if_the_user_exists()
    {
        $user = User::factory()->make();

        $response = $this->call('GET', '/api/v1/users/' . $user->id);

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

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
