<?php

namespace Integration\Services;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\UserService;
use App\Services\AuthService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Http\Controllers\Requests\User\StoreRequest;
use App\Http\Controllers\Requests\Auth\SignInRequest;

class AuthServiceTest extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_attempting_to_log_in_a_user_that_does_not_exist()
	{
		$signInRequest = new SignInRequest(new Request([
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		]));

		$service = new AuthService();
		$response = $service->signIn($signInRequest->getParams(), app(JWTAuth::class));

		$this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_200_when_attempting_to_log_in_a_real_user()
	{
		$storeRequest = new StoreRequest(new Request([
			'name' => 'test',
			'email' => 'you@test.com',
			'password' => '@X_I123^76'
		]));

		$userService = new UserService();
		$userService->store($storeRequest->getParams());


		$signInRequest = new SignInRequest(new Request([
			'email' => 'you@test.com',
			'password' => '@X_I123^76'
		]));

		$service = new AuthService();
		$response = $service->signIn($signInRequest->getParams(), app(JWTAuth::class));

		$this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
	}
}
