<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            $response = $client->request('POST',
                'https://open.tiktokapis.com/v2/oauth/token/',
                [
                    'form_params' => [
                        'client_key' => config('services.tiktok.client_id'),
                        'client_secret' => config('services.tiktok.client_secret'),
                        'code' => $code,
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => 'https://web.cookbookshq.com/callback/tiktok'
                    ],
                ]
            );

            dd($response);
        } catch (\Exception $exception) {
            dd($exception);
        }

    }
}
