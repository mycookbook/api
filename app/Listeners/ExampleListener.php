<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use Illuminate\Support\Facades\Log;

class ExampleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //		Log::info('Example event fired');
    }

    /**
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */
    public function handle(ExampleEvent $event)
    {
        //		Log::info('more', ['event' => $this->event]);
    }
}
