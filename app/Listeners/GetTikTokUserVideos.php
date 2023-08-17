<?php

namespace App\Listeners;

use App\Exceptions\TikTokException;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new Client();
        $tikTokUser = $event->tikTokUserDto;
        $context = [];

        try {
            $response = $client->request('POST',
                'https://open.tiktokapis.com/v2/video/list/?fields=cover_image_url,id,title,video_description,duration,height,width,title,embed_html,embed_link',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $tikTokUser->getCode(),
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            DB::table('tiktok_users')
                ->insert([
                    'user_id' => $event->tikTokUserDto->getUserId(),
                    'videos' => json_encode($decoded['data']['videos']),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        } catch(\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
