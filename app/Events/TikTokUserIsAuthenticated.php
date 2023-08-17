<?php

declare(strict_types=1);

namespace App\Events;

use App\Dtos\TikTokUserDto;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TikTokUserIsAuthenticated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected User $user;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TikTokUserDto $tikTokUserDto
    ) {}

    public function getUser()
    {
        return User::findOrFail($this->tikTokUserDto->getUserId());
    }
}
