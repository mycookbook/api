<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthGuard
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            /** @phpstan-ignore-next-line  */
            if (JWTAuth::parseToken()->authenticate()) {

                /** @phpstan-ignore-next-line  */
                $request->merge(["user_id" => JWTAuth::parseToken()->user()->getKey()]);

                return $next($request);
            }
        } catch (\Exception $exception) {
            throw new UnauthorizedException('Your session has expired. Please login and try again.');
        }
    }
}
