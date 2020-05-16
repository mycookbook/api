<?php


namespace Integration\Requests;

use Monolog\Test\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Requests\FormRequest;
use Laravel\Lumen\Routing\Controller as BaseController;

class BaseControllerFormRequestTest extends TestCase
{
	/**
	 * @test
	 */
	public function it_is_initializable()
	{
		$request = new Request([
			'alpha' => 'alpha',
			'beta' => 'beta'
		]);

		$baseControllerFormReq = new Controller($request);

		$this->assertInstanceOf(BaseController::class, $baseControllerFormReq);
		$this->assertInstanceOf(FormRequest::class, $baseControllerFormReq);
	}

	/**
	 * @test
	 */
	public function the_form_request_params_is_an_instance_of_illuminate_request()
	{
		$request = new Request([
			'alpha' => 'alpha',
			'beta' => 'beta'
		]);

		$baseControllerFormReq = new Controller($request);

		$this->assertInstanceOf(Request::class, $baseControllerFormReq->getParams());

		$requestParams = $baseControllerFormReq->getParams();
		$this->assertSame('alpha', $requestParams->get('alpha'));
		$this->assertSame('beta', $requestParams->get('beta'));
	}
}