<?php

namespace App\Jobs;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Traits\EncryptsPayload;

class SendNotification implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels, EncryptsPayload;

	protected $type;
	protected $uri;
	protected $id;

	/**
	 * SendNotification constructor.
	 *
	 * @param $type
	 * @param $id
	 */
	public function __construct($type, $id)
	{
		//TODO: other types will be supported in the future e.g sms in-app
		//the payload will be different in each case
		//for example an sms type requires phone

		$this->type = $type;
		$this->id = $id;
		$this->uri = env('NOTIFICATIONS_SERVER_URL') . '/events';
	}

	/**
	 * Job Handler
	 */
	public function handle()
	{
		try {
			$user = User::findOrFail($this->id);

			$options['headers'] = [
				'payload' => $this->payload([
					'type' => $this->type,
					'email' => $user->email,
					'username' => $user->name
				])
			];

			$client = new Client();

			$client->request('GET', $this->uri, $options);

		} catch(Exception $e) {
			Log::info('error', ['notifications_server' => $e->getMessage()]);
		}
	}
}
