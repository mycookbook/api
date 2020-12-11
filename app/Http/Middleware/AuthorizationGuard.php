<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PurgeUnauthorizedRequest
{
	/**
	 * Handle an incoming request. Based on Asm89\Stack\Cors by asm89
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 *
	 * @return Response
	 */
	public function handle(Request $request, Closure $next)
	{
		if (!$request->header('X-API-KEY')) {
			Log::info([
				'type' => 'unauthorized request. does not contain api-key header',
				'timestamp' => Carbon::now()
			]);

			return response(
				[
					'message' => [
						'type' => 'warning',
						'content' => 'Unrecognized request.'
					]
				], \Illuminate\Http\Response::HTTP_UNAUTHORIZED
			);
		}

		return $next($request);
	}
}