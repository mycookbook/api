<?php

namespace Functional\Integration\Requests\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\User\StoreRequest;

class StoreRequestTest extends \TestCase
{

	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$request = new StoreRequest(new Request([
			'name' => 'test',
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_name_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => '',
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_name_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_email_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name',
			'email' => '',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_email_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_email_is_not_a_valid_email_format()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name 2',
			'email' => 'not a valid email',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_email_already_exists()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$user = new User([
			'name' => 'test',
			'email' => 'you@test.com',
			'password' => 'randomString123',
			'following' => 0,
			'followers' => 0
		]); //use the User factory when u refactor

		$user->save();

		$request = new StoreRequest(new Request([
			'name' => 'test 2',
			'email' => 'you@test.com',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name',
			'email' => 'you@test.com',
			'password' => ''
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name',
			'email' => 'you@test.com'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_less_than_5_characters_long()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new StoreRequest(new Request([
			'name' => 'test name',
			'email' => 'you@test.com',
			'password' => '1234'
		]));
	}

	/**
	 * @test
	 */
	public function it_can_get_the_request_object()
	{
		$requestData = [
			'name' => 'test X',
			'email' => 'x@test.com',
			'password' => '@34v_reT6543'
		];

		$storeRequest = new StoreRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['name'], $storeRequest->getParams()->input('name'));
		$this->assertSame($requestData['email'], $storeRequest->getParams()->input('email'));
		$this->assertSame($requestData['password'], $storeRequest->getParams()->input('password'));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_request_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$storeRequest = new StoreRequest(new Request([]));
	}
}
