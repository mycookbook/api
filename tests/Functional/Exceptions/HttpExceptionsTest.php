<?php

use Illuminate\Http\Response;

/**
 * Class HttpExceptionsTest
 */
class HttpExceptionsTest extends TestCase
{
    /**
     * Test MethodNotAllowedException
     *
     * @return void
     */
    public function testMethodNotAllowedException()
    {
        $response = $this->call('POST', '/api/v1');

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->status());

        $this->seeJsonStructure(
            [
                'status', 'message', 'docs'
            ]
        );
    }

    /**
     * Test testNotFoundHttpException
     *
     * @return void
     */
    public function testNotFoundHttpException()
    {
        $response = $this->call('GET', '/api/v1/notfound');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());

        $this->seeJsonStructure(
            [
                'status', 'message', 'docs'
            ]
        );
    }

	/**
	 * @test
	 */
    public function it_responds_with_a_404_when_trying_to_login_with_invalid_credentials()
	{
		$response = $this->call('POST', '/api/v1/auth/login', ['email' => 'invalid-email', 'password' => 'invalid-password']);

		$this->assertEquals(Response::HTTP_NOT_FOUND, $response->status());
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_when_trying_to_login_without_credentials()
	{
		$response = $this->call('POST', '/api/v1/auth/login', []);

		$this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->status());
	}
}
