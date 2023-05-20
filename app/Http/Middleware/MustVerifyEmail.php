<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MustVerifyEmail
{
    /**
     * Run the request filter.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return response([
                'error' => 'You have not verified your email yet.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
