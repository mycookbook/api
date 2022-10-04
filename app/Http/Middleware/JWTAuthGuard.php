<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JWTAuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        if (!$token = $request->bearerToken()) {
//            return response()->json([
//                'error' => 'Invalid request.'
//            ], 401);
//        }

        return $next($request);
    }
}
