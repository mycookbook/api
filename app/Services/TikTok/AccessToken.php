<?php

namespace App\Services\TikTok;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;

class AccessToken extends Request
{
    public function handle(): void
    {
        $firstRequest = $this->httpClient->post('https://open.tiktokapis.com/v2/oauth/token/', [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'client_key' => config('services.tiktok.client_id'),
                'client_secret' => config('services.tiktok.client_secret'),
                'code' => Config::get('tiktok')['code'],
                'grant_type' => 'authorization_code',
                'redirect_uri' => ''
            ],
        ]);

        $decoded = json_decode($firstRequest->getBody()->getContents(), true);

        if ($decoded["error"]) {
            if ($exception = json_encode($decoded)) {
                throw new InvalidArgumentException($exception);
            }
        }

        Config::set('tiktok', ['access_token' => $decoded['access_token']]);
    }
}
