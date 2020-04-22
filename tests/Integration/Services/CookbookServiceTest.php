<?php

namespace Integration\Services;

use App\Flag;
use App\User;
use App\Category;
use App\Cookbook;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CookbookService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Http\Controllers\Requests\Cookbook\StoreRequest;

class CookbookServiceTest extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_responds_with_a_200_when_retrieving_all_cookbooks()
	{
		$service = new CookbookService();
		$response = $service->index();
		$this->assertSame(Response::HTTP_OK, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_an_unauthenticated_user_attempts_to_create_a_cookbook()
	{
		$this->expectException(\ErrorException::class);

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new StoreRequest(new Request([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $category->id,
			'flag_id' => $flag->id
		]));

		$service = new CookbookService();
		$service->store($request->getParams());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_201_when_an_authenticated_user_attempts_to_create_a_cookbook()
	{
		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$request = new Request([
			'name' => 'sample title',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $category->id,
			'flag_id' => $flag->id
		]);

		$request->setUserResolver(function () {
			$user = new User([
				'name' => 'test',
				'email' => 'you@test.com',
				'password' => '@X_I123^76',
				'following' => 0,
				'followers' => 0
			]);
			$user->save();
			return $user;
		});

		$cookbookStoreRequest = new StoreRequest($request);
		$cookbookService = new CookbookService();

		$response = $cookbookService->store($cookbookStoreRequest->getParams());
		$this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_cookbook_instance_when_retrieving_a_cookbook_that_exists()
	{
		$user = new User([
			'name' => 'test mate 2',
			'email' => 'test@mail.com',
			'password' => '@X_I123^76',
			'followers' => 13,
			'following' => 1
		]);
		$user->save();

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$cookbook = new Cookbook([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $category->id,
			'flag_id' => $flag->id,
			'user_id' => $user->id
		]);
		$cookbook->save();

		$service = new CookbookService();
		$response = $service->show($cookbook->id);

		$this->assertInstanceOf(Cookbook::class, $response);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_retrieving_a_cookbook_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new CookbookService();
		$service->show(0);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_trying_to_update_a_cookbook_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new CookbookService();

		$request = new Request([
			'name' => 'new title',
		]);

		$service->update($request, 0);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_200_when_trying_to_update_a_cookbook_that_exists()
	{
		$user = new User([
			'name' => 'test mate 2',
			'email' => 'test@mail.com',
			'password' => '@X_I123^76',
			'followers' => 13,
			'following' => 1
		]);
		$user->save();

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$cookbook = new Cookbook([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $category->id,
			'flag_id' => $flag->id,
			'user_id' => $user->id
		]);
		$cookbook->save();

		$service = new CookbookService();

		$request = new Request([
			'name' => 'new title',
		]);

		$response = $service->update($request, $cookbook->id);

		$this->assertSame(Response::HTTP_OK, $response->getStatusCode());
		$this->seeInDatabase('cookbooks', [
			'id' => $cookbook->id,
			'name' => 'new title'
		]);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_when_trying_to_delete_a_cookbook_that_does_not_exist()
	{
		$this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

		$service = new CookbookService();

		$service->delete(0);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_202_when_trying_to_delete_a_cookbook_that_exists()
	{
		$user = new User([
			'name' => 'test mate 2',
			'email' => 'test@mail.com',
			'password' => '@X_I123^76',
			'followers' => 13,
			'following' => 1
		]);
		$user->save();

		$category = new Category([
			'name' => 'test_title',
			'slug' => 'test_slug',
			'color' => '000000'
		]);
		$category->save();

		$flag = new Flag([
			'flag' => 'ug',
			'nationality' => 'Ugandan'
		]);
		$flag->save();

		$cookbook = new Cookbook([
			'name' => 'sample cookbook',
			'description' => Str::random(126),
			'bookCoverImg' => 'http://dummuy-image.jpg',
			'category_id' => $category->id,
			'flag_id' => $flag->id,
			'user_id' => $user->id
		]);
		$cookbook->save();

		$service = new CookbookService();

		$response = $service->delete($cookbook->id);

		$this->assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());
		$this->notSeeInDatabase('cookbooks', [
			'id' => $cookbook->id
		]);
	}
}
