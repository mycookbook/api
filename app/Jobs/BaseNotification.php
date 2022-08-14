<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\EncryptsPayload;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class BaseNotification implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, EncryptsPayload;

    protected $user;

    protected $payload;

    /**
     * BaseNotification constructor.
     *
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->user = User::findOrFail($userId);
    }

    /**
     * @return array
     */
    public function headerOptions(): array
    {
        return ['headers' => [
            'payload' => $this->encryptPayload($this->payload),
        ]];
    }

    /**
     * Job Handler
     */
    public function handle()
    {
        try {
            $client = new Client();
            $uri = env('NOTIFICATIONS_SERVER_URL').'/notifications';

            $client->request('GET', $uri, $this->headerOptions());
        } catch (Exception $e) {
            Log::info('error', ['notifications_server' => $e->getMessage()]);
        }
    }
}
