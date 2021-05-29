<?php

namespace Integration\Requests\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\User\UpdateRequest;

class UpdateRequestTest extends \TestCase
{

	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$request = new UpdateRequest(new Request([
			'name' => 'test2',
			'password' => 'testpassword'
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_name_slug_is_empty()
	{
		$this->expectException(\App\Exceptions\UnprocessibleEntityException::class);

		$request = new UpdateRequest(new Request([
			'name' => '',
			'password' => 'testpassword'
		]));
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_password_is_empty()
	{
		$this->expectException(\App\Exceptions\UnprocessibleEntityException::class);

		$request = new UpdateRequest(new Request([
			'name' => 'test 2',
			'password' => ''
		]));
	}

	/**
	 * @test
	 */
	public function it_can_get_the_request_object()
	{
		$requestData = [
			'name' => 'test X',
			'password' => '@34v_reT6543'
		];

		$storeRequest = new UpdateRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['name'], $storeRequest->getParams()->input('name'));
		$this->assertSame($requestData['password'], $storeRequest->getParams()->input('password'));
	}
}
