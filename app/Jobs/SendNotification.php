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

class SendNotification implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels, EncryptsPayload;

	protected $uri;
	protected $userId;
	protected $channelId;
	protected $payload;

	/**
	 * SendNotification constructor.
	 *
	 * @param $type
	 * @param $userId
	 * @param string $channelId
	 */
	public function __construct($type, $userId, $channelId = 'channel-id')
	{
		$this->payload = [
			'type' => $type
		];

		if ($type === 'in-app') {
			$this->payload['in-app'] = $channelId;
		}

		$this->userId = $userId;

		$this->uri = env('NOTIFICATIONS_SERVER_URL') . '/events';
	}

	/**
	 * Job Handler
	 */
	public function handle()
	{
		try {
			$user = User::findOrFail($this->userId);

			if ($this->payload['type'] === 'email') {
				$this->payload['email'] = $user->email;
				$this->payload['username'] = $user->name;
			}

			if ($this->payload['type'] === 'sms') {
				$this->payload['phone'] = $user->contact()->get()->first()->phone;
			}

			$options['headers'] = [
				'payload' => $this->getPayload($this->payload)
			];

			$client = new Client();

			$client->request('GET', $this->uri, $options);

		} catch(Exception $e) {
			Log::info('error', ['notifications_server' => $e->getMessage()]);
		}
	}
}
