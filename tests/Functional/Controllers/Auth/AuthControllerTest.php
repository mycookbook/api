<?php

namespace Tests\Functional\Controllers\Auth;

use Laravel\Lumen\Testing\DatabaseMigrations;

/**
 * Class UserControllerTest
 */
class AuthControllerTest extends \TestCase
{
	use DatabaseMigrations;

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_user_email_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/signin', [
				'email' => '',
				'password' => 'mypassword'
			]
		)->seeJson(
			[
				'email' => [
					'The email field is required.'
				]
			]
		)->seeStatusCode(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_user_email_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/signin', [
				'password' => 'mypassword'
			]
		)->seeJson(
			[
				'email' => [
					'The email field is required.'
				]
			]
		)->seeStatusCode(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_user_password_is_empty()
	{
		$this->json(
			'POST', '/api/v1/auth/signin', [
				'email' => 'sally@foo.com',
				'password' => ''
			]
		)->seeJson(
			[
				'password' => [
					'The password field is required.'
				]
			]
		)->seeStatusCode(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_user_password_is_null()
	{
		$this->json(
			'POST', '/api/v1/auth/signin', [
				'email' => 'sally@foo.com'
			]
		)->seeJson(
			[
				'password' => [
					'The password field is required.'
				]
			]
		)->seeStatusCode(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_422_if_the_request_does_not_contain_email_and_password_fields()
	{
		$this->json(
			'POST', '/api/v1/auth/signin', []
		)->seeJson(
			[
				'password' => [
					'The password field is required.'
				],
				'email' => [
					'The email field is required.'
				]
			]
		)->seeStatusCode(422);
	}

	/**
	 * @test
	 */
	public function it_responds_with_a_404_when_attempting_to_signin_a_user_that_does_not_exist()
	{
		$this->json(
			'POST', '/api/v1/auth/signup', [
				'name' => 'Sally',
				'email' => 'sally@foo.com',
				'password' => 'salis'
			]
		);

		$this->json(
			'POST', '/api/v1/auth/signin', [
				'email' => 'sally@foo.com',
				'password' => 'invalidpassword'
			]
		)->seeJson(
			[
				'Not found or Invalid Credentials.'
			]
		)->seeStatusCode(404);
	}
}
