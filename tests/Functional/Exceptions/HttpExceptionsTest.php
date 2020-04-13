<?php

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

        $this->assertEquals(405, $response->status());

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

        $this->assertEquals(404, $response->status());

        $this->seeJsonStructure(
            [
                'status', 'message', 'docs'
            ]
        );
    }
}
