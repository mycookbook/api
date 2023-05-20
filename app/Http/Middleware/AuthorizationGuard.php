<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationGuard
{
    /**
     * Handle an incoming request. Based on Asm89\Stack\Cors by asm89
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
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

        //		$token = JWTAuth::getToken();
//
        //		$user_id = JWTAuth::getPayload($token)->toArray()["sub"];
//
        //		$request->merge(["user_id" => $user_id]);

        return $next($request);
    }
}
