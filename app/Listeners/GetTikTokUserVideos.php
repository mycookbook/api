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
                'https://open.tiktokapis.com/v2/video/list/?fields=cover_image_url,id,title',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $tikTokUser->getCode(),
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            dd(array_merge(['code' => $tikTokUser->getCode()], $decoded));
        } catch(\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
