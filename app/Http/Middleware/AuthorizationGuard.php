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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
		if (!$request->header('X-API-KEY') || !$request->header('X-CLIENT-SECRET')) {
			Log::alert([
				'type' => 'Unauthorized',
				'content' => 'The header does not contain an api-key',
				'timestamp' => Carbon::now()->toDateTimeString()
			]);

			throw new UnauthorizedClientException();
		}

		$client = AuthorizedClient::where(['api_key' => $request->header('X-API-KEY')]);

		if (!$client->get()->first()) {
			throw new NotFoundHttpException();
		}

		try {
			$decrypted = Crypt::decrypt($request->header('X-CLIENT-SECRET'));
			$payload = explode(".", $decrypted);

			if ($payload[0] !== $request->header('X-API-KEY') || $payload[1] !== $client->get()->first()->passphrase) {
				throw new UnauthorizedClientException();
			}
		} catch (DecryptException $e) {
			throw new UnauthorizedClientException();
		}

		return $next($request);
	}
}