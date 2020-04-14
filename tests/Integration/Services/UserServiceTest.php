<?php

namespace Integration\Services;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\UserService;
use App\Interfaces\serviceInterface;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Http\Controllers\Requests\User\StoreRequest;
use App\Http\Controllers\Requests\User\UpdateRequest;

class UserServiceTest extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_adheres_to_the_common_service_interface()
	{
		$service = new UserService();
		$this->assertInstanceOf(serviceInterface::class, $service);
	}

	/**
	 * @test
	 */
	public function it_responds_with_200_when_retrieving_all_users()
	{
		$service = new UserService();
		$response = $service->index();

		$this->assertSame($response->getStatusCode(), Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function it_can_create_a_new_user_resource()
	{
		$storeRequest = new StoreRequest(new Request([
			'name' => 'test',
			'email' => 'you@test.com',
			'password' => '@X_I123^76'
		]));

		$service = new UserService();
		$response = $service->store($storeRequest->getParams());

		$this->assertSame($response->getStatusCode(), Response::HTTP_CREATED);
	}

	/**
	 * @test
	 */
	public function it_can_retrieve_a_single_user_resource_that_exists()
	{
		$storeRequest = new StoreRequest(new Request([
			'name' => 'test mate',
			'email' => 'you@test.com',
			'password' => '@X_I123^76'
		]));

		$service = new UserService();
		$service->store($storeRequest->getParams());

		$response = $service->show('test-mate');

		$this->assertSame($response->getStatusCode(), Response::HTTP_OK);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_resource_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new UserService();
		$service->show('test-mate');
	}

	/**
	 * @test
	 */
	public function it_can_update_an_existing_resource()
	{
		$storeRequest = new StoreRequest(new Request([
			'name' => 'test mate',
			'email' => 'you@test.com',
			'password' => '@X_I123^76'
		]));

		$service = new UserService();
		$service->store($storeRequest->getParams());

		$updateRequest = new UpdateRequest(new Request([
			'name' => 'test mate 2',
			'password' => '@X_I123^76',
			'followers' => 13,
			'following' => 1
		]));

		$response= $service->update($updateRequest->getParams(), 'test-mate');
		$user = User::where('email', 'you@test.com')->first();

		$this->assertSame($response->getStatusCode(), Response::HTTP_OK);
		$this->assertSame($user->name_slug, 'test-mate-2');
		$this->assertSame((int)$user->followers, 13);
		$this->assertSame((int)$user->following, 1);
	}
}