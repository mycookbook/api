<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserIsAuthenticated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SaveTikTokCode
{
    public function handle(UserIsAuthenticated $event): void
    {
        $code = Crypt::encryptString($event->code);
        $user_id = $event->user->getKey();
        $db =  DB::table('tiktok_users');
        $timestamps = [
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ];

        $tiktok_user = DB::table('tiktok_users')->where(["user_id" => $user_id])->first();

        if ($tiktok_user === null) {
            $db->insert(array_merge(['user_id' => $user_id, 'code' => $code], $timestamps));
        } else {
            $db->update(array_merge(['code' => $code], $timestamps));
        }
    }
}
