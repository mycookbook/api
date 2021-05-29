<?php

namespace Functional\Middleware;

use App\AuthorizedClient;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthGuardMiddlewareTest extends \TestCase
{

	public function public_cookbooks_routes()
	{
		$this->it_responds_with_401_if_the_api_key_and_client_secret_are_not_given();
		$this->it_responds_with_401_if_the_api_key_is_not_given_but_the_client_secret_is_given();
		$this->it_responds_with_401_if_the_api_key_is_given_but_the_client_secret_is_not_given();
		$this->it_responds_with_404_if_the_given_api_key_is_invalid();
		$this->it_responds_with_401_if_the_given_client_secret_is_invalid();
	}

	//TODO:
	public function protected_cookbooks_routes() {}
	public function public_recipes_routes() {}
	public function protected_recipes_routes() {}
	public function public_users_routes() {}
	public function protected_users_routes() {}
	public function other_public_routes() {}

	public function it_responds_with_401_if_the_api_key_and_client_secret_are_not_given()
	{
		$this->get('/api/v1/cookbooks')->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
	}

	public function it_responds_with_401_if_the_api_key_is_not_given_but_the_client_secret_is_given()
	{
		$this->get('/api/v1/cookbooks', ['X-CLIENT-SECRET' => 'client-secret'])
			->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
	}

	public function it_responds_with_401_if_the_api_key_is_given_but_the_client_secret_is_not_given()
	{
		$this->get('/api/v1/cookbooks', ['X-API-KEY' => 'api-key'])
			->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
	}

	public function it_responds_with_404_if_the_given_api_key_is_invalid()
	{
		$api_key = Str::random(50);
		$passphrase = Str::random(10);
		$secret = Crypt::encrypt($api_key . "." . $passphrase);

		$client = new AuthorizedClient([
			'api_key' => $api_key,
			'passphrase' => $passphrase,
			'client_secret' => $secret
		]);

		$client->save();

		$this->get('/api/v1/cookbooks', [
			'X-API-KEY' => 'invalid-api-key',
			'X-CLIENT-SECRET' => $secret
		])->assertResponseStatus(Response::HTTP_NOT_FOUND);
	}

	public function it_responds_with_401_if_the_given_client_secret_is_invalid()
	{
		$api_key = Str::random(50);
		$passphrase = Str::random(10);
		$secret = Crypt::encrypt($api_key . "." . $passphrase);

		$client = new AuthorizedClient([
			'api_key' => $api_key,
			'passphrase' => $passphrase,
			'client_secret' => $secret
		]);

		$client->save();

		$this->get('/api/v1/cookbooks', [
			'X-API-KEY' => $api_key,
			'X-CLIENT-SECRET' => "invalid-client-secret"
		])->assertResponseStatus(Response::HTTP_UNAUTHORIZED);
	}
}