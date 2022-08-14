<?php

namespace Integration\Requests;

use App\Http\Requests\SignInRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class SignInRequestTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_cookbook_form_request()
    {
        $request = new SignInRequest();

        $this->assertInstanceOf(FormRequest::class, $request);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_email_is_empty()
    {
        $request = new SignInRequest();

        $validator = Validator::make([
            'email' => '',
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email field is required.", $validator->errors()->toArray()["email"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_email_is_null()
    {
        $request = new SignInRequest();

        $validator = Validator::make([
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email field is required.", $validator->errors()->toArray()["email"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_password_is_empty()
    {
        $request = new SignInRequest();

        $validator = Validator::make([
            'email' => 'test@mail.ca',
            'password' => '',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('password', $validator->errors()->keys());
        $this->assertSame("The password field is required.", $validator->errors()->toArray()["password"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_password_is_null()
    {
        $request = new SignInRequest();

        $validator = Validator::make([
            'email' => 'test@mail.ca',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('password', $validator->errors()->keys());
        $this->assertSame("The password field is required.", $validator->errors()->toArray()["password"][0]);
    }

    /**
     * @test
     */
    public function it_returns_the_request_object()
    {
        $requestData = [
            'email' => 'test@mail.ca',
            'password' => 'testpassword',
        ];

        $storeRequest = new SignInRequest();

        $validator = Validator::make($requestData, $storeRequest->rules());

        $this->assertInstanceOf(FormRequest::class, $storeRequest);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors()->toArray());
    }
}
