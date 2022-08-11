<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptsPayload
{
    /**
     * Encrypts the given payload using Crypt
     *
     * @param  array  $payload
     * @return string
     */
    public function encryptPayload(array $payload): string
    {
        $payload['secret'] = env('CRYPT_SECRET');

        return Crypt::encrypt($payload);
    }
}
