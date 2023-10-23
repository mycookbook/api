<?php

namespace App\Listeners;

use App\Http\Clients\Enums\VideoListEnum;
use App\Http\Clients\TikTokHttpClient;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetTikTokUserVideos
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $client = new TikTokHttpClient(new Client());
        $videos = [];

        try {
            $decoded = $videos = $client->listVideos($event->tikTokUserDto);
            $db = DB::table('tiktok_users');

            $tiktok_user = $db->where(['user_id' => $event->tikTokUserDto->user_id])->first();
            $data = [
                'videos' => json_encode($decoded['data']['videos']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            if ($tiktok_user === null) {
                $data['user_id'] = $event->tikTokUserDto->getUserId();
                $db->insert($data);
            } else {
                $db->update($data);
            }
        } catch(\Exception $exception) {
            Log::debug(
                'Error listing TikTok Videos',
                [
                    'errorMsg' => $exception->getMessage(),
                    'fields' => VideoListEnum::values(),
                    'videos' => $videos
                ]
            );
        }
    }
}
