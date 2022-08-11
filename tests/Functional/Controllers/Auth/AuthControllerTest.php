<?php

namespace Functional\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutMiddleware;

/**
 * Class UserControllerTest
 */
class AuthControllerTest extends \TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_user_email_is_empty()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => '',
                'password' => 'mypassword',
            ]
        )->seeJson(
            [
                'email' => [
                    'The email field is required.',
                ],
            ]
        )->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_user_email_is_null()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'password' => 'mypassword',
            ]
        )->seeJson(
            [
                'email' => [
                    'The email field is required.',
                ],
            ]
        )->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_user_password_is_empty()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
                'password' => '',
            ]
        )->seeJson(
            [
                'password' => [
                    'The password field is required.',
                ],
            ]
        )->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_user_password_is_null()
    {
        $this->json(
            'POST', '/api/v1/auth/login', [
                'email' => 'sally@foo.com',
            ]
        )->seeJson(
            [
                'password' => [
                    'The password field is required.',
                ],
            ]
        )->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function it_responds_with_a_422_if_the_request_does_not_contain_email_and_password_fields()
    {
        $this->json(
            'POST', '/api/v1/auth/login', []
        )->seeJson(
            [
                'password' => [
                    'The password field is required.',
                ],
                'email' => [
                    'The email field is required.',
                ],
            ]
        )->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        )->seeJson(
            [
                'Not found or Invalid Credentials.',
            ]
        )->seeStatusCode(Response::HTTP_NOT_FOUND);
    }
}
