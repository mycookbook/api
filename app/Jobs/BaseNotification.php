<?php

namespace App\Jobs;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use App\Traits\EncryptsPayload;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class BaseNotification implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels, EncryptsPayload;

	protected $user;
	protected $payload;

	/**
	 * BaseNotification constructor.
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
			'payload' => $this->encryptPayload($this->payload)
		]];
	}

	/**
	 * Job Handler
	 */
	public function handle()
	{
		try {
			$client = new Client();
			$uri = env('NOTIFICATIONS_SERVER_URL') . '/notifications';

			$client->request('GET', $uri, $this->headerOptions());

		} catch(Exception $e) {
			Log::info('error', ['notifications_server' => $e->getMessage()]);
		}
	}
}
