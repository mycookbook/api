<?php

namespace App\Listeners;

use GuzzleHttp\Client;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new Client();
        $tiktokUser = $event->getUser();

        $response = $client->request('POST',
            'https://open.tiktokapis.com/v2/oauth/token/',
            [
                'form_params' => [
                    'client_key' => config('services.tiktok.client_id'),
                    'client_secret' => config('services.tiktok.client_secret'),
                    'code' => $tiktokUser->getCode(),
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => 'https://web.cookbookshq.com/callback/tiktok'
                ],
            ]
        );

        $decoded = json_decode($response->getBody()->getContents(), true);

        dd(array_merge(['code' => $tiktokUser->getCode()], $decoded));
    }
}
