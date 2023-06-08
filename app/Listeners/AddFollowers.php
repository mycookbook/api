<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserIsAuthenticated;
use App\Models\Following;
use App\Models\User;

class AddFollowers
{
    /**
     * Handle the event.
     */
    public function handle(UserIsAuthenticated $event): void
    {
        $user = User::find($event->user);

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
