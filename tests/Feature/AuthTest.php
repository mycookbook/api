<?php

declare(strict_types=1);

namespace Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

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
        )->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('email', $decoded);
        $this->assertSame("The email field is required.", $decoded["email"][0]);
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
        )->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('email', $decoded);
        $this->assertSame("The email field is required.", $decoded["email"][0]);
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
        )->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('password', $decoded);
        $this->assertSame("The password field is required.", $decoded["password"][0]);
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
        )->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('password', $decoded);
        $this->assertSame("The password field is required.", $decoded["password"][0]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_if_the_request_does_not_contain_email_and_password_fields()
    {
        $response = $this->json(
            'POST', '/api/v1/auth/login', []
        )->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('email', $decoded);
        $this->assertSame("The email field is required.", $decoded["email"][0]);

        $this->assertArrayHasKey('password', $decoded);
        $this->assertSame("The password field is required.", $decoded["password"][0]);
    }

    /**
     * @test
     */
    public function it_can_successfully_register_a_new_user()
    {
        $this->assertDatabaseMissing('users', [
            'name' => 'Sally',
            'email' => 'sally@foo.com',
        ]);

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salispa$$',
            ]
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Sally',
            'email' => 'sally@foo.com',
        ]);
    }

    /**
     * @test
     */
    public function it_responds_with_an_error_when_attempting_to_register_an_existing_user_email()
    {
        $user = User::factory()->make([
            'email' => 'sally@foo.com'
        ]);

        $user->save();

        $response = $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally',
                'email' => 'sally@foo.com',
                'password' => 'salispa$$',
            ]
        );

        $decoded = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("email", $decoded);
        $this->assertSame("The email has already been taken.", $decoded['email'][0]);
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
        )->assertStatus(ResponseAlias::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_can_successfully_authenticate_an_existing_user_and_responds_with_a_token()
    {
        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $res = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@example.com',
                'password' => 'saltyL@k3',
            ]
        );

        $decoded = json_decode($res->getContent(), true);
        $this->assertArrayHasKey("token", $decoded);
        $this->assertNotEmpty($decoded["token"]);
    }

    /**
     * @test
     */
    public function it_can_logout_an_existing_user()
    {
        $email = 'sally@example.com';
        $password = 'saltyL@k3';

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => $email,
                'password' => $password,
            ]
        );

        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => $email,
                'password' => $password,
            ]
        );

        $decoded = json_decode($response->getContent(), true);

        $this->json(
            'GET', '/api/v1/auth/logout', [], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $decoded['token']
                ]
            ]
        )->assertNoContent();
    }

    /**
     * @test
     */
    public function logout_responds_with_an_error_if_token_is_invalid()
    {
        Log::shouldReceive('info')
            ->once()
            ->with(
                'Not found or Invalid Credentials.',
                [
                    'errorMsg' => 'Token could not be parsed from the request.'
                ]
            );

        $this->withoutExceptionHandling()
            ->json(
            'GET', '/api/v1/auth/logout', [], [
                'headers' => [
                    'Authorization' => 'Bearer invalid-token'
                ]
            ]
        )
            ->assertStatus(ResponseAlias::HTTP_BAD_REQUEST)
            ->assertExactJson([
                'Not found or Invalid Credentials.'
            ]);
    }

    /**
     * @test
     */
    public function it_can_validate_access_token_success()
    {
        $email = 'sally@example.com';
        $password = 'saltyL@k3';

        $this->json(
            'POST', '/api/v1/auth/register', [
                'name' => 'Sally Lee',
                'email' => $email,
                'password' => $password,
            ]
        );

        $response = $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => $email,
                'password' => $password,
            ]
        );

        $decoded = json_decode($response->getContent(), true);

        $this
            ->json(
                'POST', '/api/v1/auth/validate', [], [
                    'Authorization' => 'Bearer ' . $decoded['token']
                ]
            )
            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertExactJson([
                'validated' => true
            ]);
    }

    /**
     * @test
     */
    public function it_can_validate_access_token_fails()
    {
        $this
            ->json(
                'POST', '/api/v1/auth/validate', [], [
                    'Authorization' => 'Bearer invalid-token'
                ]
            )
            ->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED)
            ->assertExactJson([
                'error' => 'Expired or Invalid token.'
            ]);
    }
}
