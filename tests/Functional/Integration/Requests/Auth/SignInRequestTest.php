<?php

namespace Functional\Integration\Requests\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\Auth\SignInRequest;

class SignInRequestTest extends \TestCase
{
	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$request = new SignInRequest(new Request([
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_request_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new SignInRequest(new Request([]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_email_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new SignInRequest(new Request([
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

		$request = new SignInRequest(new Request([
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new SignInRequest(new Request([
			'email' => 'test@mail.ca',
			'password' => ''
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_null()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		$request = new SignInRequest(new Request([
			'email' => 'test@mail.ca'
		]));
	}

	/**
	 * @test
	 */
	public function it_returns_the_request_object()
	{
		$requestData = [
			'email' => 'test@mail.ca',
			'password' => 'testpassword'
		];

		$storeRequest = new SignInRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());

		$this->assertSame($requestData['email'], $storeRequest->getParams()->input('email'));
		$this->assertSame($requestData['password'], $storeRequest->getParams()->input('password'));
	}
}
