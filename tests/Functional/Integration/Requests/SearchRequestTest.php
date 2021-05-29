<?php

namespace Functional\Integration\Requests;

use Illuminate\Http\Request;
use App\Http\Controllers\Requests\FormRequest;
use App\Http\Controllers\Requests\SearchRequest;

class SearchRequestTest extends \TestCase
{
	/**
	 * @test
	 */
	public function it_throws_an_exception_if_the_request_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new SearchRequest(new Request([]));
	}

	/**
	 * @test
	 */
	public function it_is_an_instance_of_cookbook_form_request()
	{
		$request = new SearchRequest(new Request([
			'query' => 'testing',
		]));

		$this->assertInstanceOf(FormRequest::class, $request);
	}

	/**
	 * @test
	 */
	public function it_throws_an_exception_if_query_field_is_empty()
	{
		$this->expectException(\Illuminate\Validation\ValidationException::class);

		new SearchRequest(new Request([
			'query' => '',
		]));
	}

	/**
	 * @test
	 */
	public function it_returns_the_request_object()
	{
		$requestData = [
			'query' => 'testing',
		];

		$storeRequest = new SearchRequest(new Request($requestData));

		$this->assertInstanceOf(Request::class, $storeRequest->getParams());
		$this->assertSame($requestData['query'], $storeRequest->getParams()->input('query'));
	}
}