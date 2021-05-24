<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\AuthorizedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\UnauthorizedClientException;
use Illuminate\Contracts\Encryption\DecryptException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizationGuard
{
	/**
	 * Handle an incoming request. Based on Asm89\Stack\Cors by asm89
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 *
	 * @return Response
	 *
	 * @throws UnauthorizedClientException
	 */
	public function handle(Request $request, Closure $next)
	{
//		if (!$request->header('X-API-KEY')) {
//			throw new UnauthorizedClientException();
//		}
//
//		$client = AuthorizedClient::where(['api_key' => $request->header('X-API-KEY')]);
//
//		if (!$client->get()->first()) {
//			throw new UnauthorizedClientException();
//		}

//		try {
//			$decrypted = Crypt::decrypt($client->get()->first()->client_secret);
//			$payload = explode(".", $decrypted);
//
//			if ($payload[0] !== $request->header('X-API-KEY') || $payload[1] !== $client->get()->first()->passphrase) {
//				throw new UnauthorizedClientException();
//			}
//		} catch (DecryptException $e) {
//			throw new UnauthorizedClientException();
//		}

		$token = JWTAuth::getToken();
		$user_id = JWTAuth::getPayload($token)->toArray()["sub"];

		$request->merge(["user_id" => $user_id]);


		return $next($request);
	}
}
