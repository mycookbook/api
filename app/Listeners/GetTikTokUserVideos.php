<?php

namespace App\Listeners;

use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;
use function Symfony\Component\Translation\t;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new Client();
        $code = DB::table('tiktok_users')->where(['user_id' => $event->getUser()->getkey()])->first();
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

            dd(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            dd([
                'e' => $exception,
                'code' => $code
            ]);
        }
    }
}
