<?php

namespace App\Jobs;

use App\Services\UserContactDetailsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserContactDetailJob implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	protected $request;

	/**
	 * Create a new job instance.
	 * @param array $request
	 */
	public function __construct(array $request)
	{
		$this->request = $request;
	}

	/**
	 * Handle method
	 */
	public function handle()
	{
		$service = new UserContactDetailsService();
		$service->updateUserSettings(new Request($this->request));
	}

}