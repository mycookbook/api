<?php

namespace App\Jobs;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendNotification implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

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
		$this->type = $type;
		$this->id = $id;
		$this->uri = env('NOTIFICATIONS_SERVER_URL');
	}

	/**
	 * Job Handler
	 */
	public function handle()
	{
		try {
			$user = User::findOrFail($this->id);

			$client = new Client();
			$client->request(
				'GET',
				$this->uri . '?type=email&email=' . $user->email . '&username=' . $user->name
			);
		} catch(Exception $e) {
			Log::info('error', ['notifications_server' => $e->getMessage()]);
		}
	}
}
