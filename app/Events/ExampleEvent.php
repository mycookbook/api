<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class ExampleEvent extends Event implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $username;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
        $this->message = "{$username} liked your status";

        //		Log::info('driver-info', ['driver' => $this->driver]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['status-liked'];
    }
}
