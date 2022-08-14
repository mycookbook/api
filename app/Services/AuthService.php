<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Authenticate the user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'Not found or Invalid Credentials.',
                ], Response::HTTP_NOT_FOUND
            );
        }

//        'username' => Auth::user()->getSlug()

        return response()->json(['token' => $token], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function logout()
    {
        try {
            Auth::logout();

            return response()->noContent();
        } catch (\Exception $exception) {
            return response()->json(
                [
                    'Not found or Invalid Credentials.'
                ], Response::HTTP_BAD_REQUEST
            );
        }
    }
}
