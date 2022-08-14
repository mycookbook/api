<?php

namespace Functional;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;

/**
 * Class UserTest
 */
class AuthTest extends \TestCase
{
    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_user_email_is_empty()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => '',
                'password' => 'mypassword',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('email', $decoded['errors']);
        $this->assertSame("The email field is required.", $decoded["message"]);
        $this->assertSame("The email field is required.", $decoded["errors"]["email"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_user_email_is_null()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'password' => 'mypassword',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('email', $decoded['errors']);
        $this->assertSame("The email field is required.", $decoded["message"]);
        $this->assertSame("The email field is required.", $decoded["errors"]["email"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_user_password_is_empty()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => '',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('password', $decoded['errors']);
        $this->assertSame("The password field is required.", $decoded["message"]);
        $this->assertSame("The password field is required.", $decoded["errors"]["password"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_user_password_is_null()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
            ]
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('password', $decoded['errors']);
        $this->assertSame("The password field is required.", $decoded["message"]);
        $this->assertSame("The password field is required.", $decoded["errors"]["password"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_request_does_not_contain_email_and_password_fields()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', []
        )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('email', $decoded['errors']);
        $this->assertSame("The email field is required.", $decoded["errors"]["email"][0]);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('errors', $decoded);
        $this->assertArrayHasKey('password', $decoded['errors']);
        $this->assertSame("The password field is required.", $decoded["errors"]["password"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_a_404_when_attempting_to_signin_a_user_that_does_not_exist()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salis',
            ]
        );

        $user = User::where(['email' => 'sally@foo.com'])->get()->first();

        $user->update([
            'email_verified' => Carbon::now(),
        ]);

        $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => 'invalidpassword',
            ]
        )->assertStatus(Response::HTTP_NOT_FOUND);
    }
}