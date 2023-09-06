<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(Request $request)
    {
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return false;
        }

        return $token;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        try {
            Auth::logout();
        } catch (\Exception $exception) {
            Log::info(
                'Not found or Invalid Credentials.',
                [
                    'errorMsg' => $exception->getMessage()
                ]
            );

            return false;
        }

        return true;
    }
}
