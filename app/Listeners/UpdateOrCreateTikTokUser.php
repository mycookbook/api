<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TikTokUserIsAuthenticated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOrCreateTikTokUser
{
    public function handle(TikTokUserIsAuthenticated $event): void
    {
        $user_id = $event->tikTokUserDto->getUserId();
        $db = DB::table('tiktok_users');
        $attributes = $event->tikTokUserDto->toArray();
        $timestamps = [
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ];

        $data = array_merge($attributes, $timestamps);

        $tiktok_user = DB::table('tiktok_users')->where(["user_id" => $user_id])->first();

        try {
            if ($tiktok_user === null) {
                $db->insert($data);
            } else {
                $db->update($data);
            }
        } catch (\Exception $exception) {
            Log::debug("Failed to create or update tiktok user", ['exception' => $exception]);
        }
    }
}
