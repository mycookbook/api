<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TikTokUserIsAuthenticated;
use App\Models\Following;
use App\Models\User;

class AddFollowers
{
    /**
     * Handle the event.
     */
    public function handle(TikTokUserIsAuthenticated $event): void
    {
        $user = User::find($event->getUser()->getKey());

        if ($user->followers == 0) {
            $follow = new Following(
                [
                    'follower_id' => $user->getKey(),
                    'following' => 31
                ]
            );
            $follow->save();
            $user->update(['following' => 1]);
        }
    }
}
