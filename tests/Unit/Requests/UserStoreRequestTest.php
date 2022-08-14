<?php

namespace Unit\Requests;

use App\Http\Requests\UserStoreRequest;
use Illuminate\Foundation\Http\FormRequest;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserStoreRequestTest extends \TestCase
{
    /**
     * @test
     */
    public function it_is_an_instance_of_cookbook_form_request()
    {
        $request = new UserStoreRequest();

        $this->assertInstanceOf(FormRequest::class, $request);
    }

    /**
     * @test
     */
    public function it_responds_with_errors_if_name_is_empty()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('name', $validator->errors()->keys());
        $this->assertSame("The name field is required.", $validator->errors()->toArray()["name"][0]);

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email field is required.", $validator->errors()->toArray()["email"][0]);

        $this->assertContains('password', $validator->errors()->keys());
        $this->assertSame("The password field is required.", $validator->errors()->toArray()["password"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_name_is_null()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'email' => 'test@mail.ca',
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('name', $validator->errors()->keys());
        $this->assertSame("The name field is required.", $validator->errors()->toArray()["name"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_email_is_empty()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name',
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
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name',
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email field is required.", $validator->errors()->toArray()["email"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_email_is_not_a_valid_email_format()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name 2',
            'email' => 'not a valid email',
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email must be a valid email address.", $validator->errors()->toArray()["email"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_email_already_exists()
    {
        $user = new User([
            'name' => 'test',
            'email' => 'you@test.com',
            'password' => 'randomString123',
            'following' => 0,
            'followers' => 0,
        ]); //use the User factory when u refactor

        $user->save();

        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test 2',
            'email' => 'you@test.com',
            'password' => 'testpassword',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('email', $validator->errors()->keys());
        $this->assertSame("The email has already been taken.", $validator->errors()->toArray()["email"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_password_is_empty()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name',
            'email' => 'you@test.com',
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
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name',
            'email' => 'you@test.com',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('password', $validator->errors()->keys());
        $this->assertSame("The password field is required.", $validator->errors()->toArray()["password"][0]);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_password_is_less_than_5_characters_long()
    {
        $request = new UserStoreRequest();

        $validator = Validator::make([
            'name' => 'test name',
            'email' => 'you@test.com',
            'password' => '1234',
        ], $request->rules());

        $this->assertFalse($validator->passes());

        $this->assertContains('password', $validator->errors()->keys());
        $this->assertSame("The password must be at least 5 characters.", $validator->errors()->toArray()["password"][0]);
    }
}
