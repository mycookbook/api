<?php

namespace App\Listeners;

use App\Exceptions\TikTokException;
use GuzzleHttp\Client;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new Client();
        $tikTokUser = $event->tikTokUserDto;

        try {
            $response = $client->request('POST',
                'https://open.tiktokapis.com/v2/oauth/token/',
                [
                    'form_params' => [
                        'client_key' => config('services.tiktok.client_id'),
                        'client_secret' => config('services.tiktok.client_secret'),
                        'code' => $tikTokUser->getCode(),
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => 'https://web.cookbookshq.com/callback/tiktok'
                    ],
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            dd(array_merge(['code' => $tikTokUser->getCode()], $decoded));
        } catch(\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
