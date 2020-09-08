<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\UserContactDetailsService;

class CreateUserContactDetail implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	protected $request;
	protected $service;
	protected $serialized;

	/**
	 * Create a new job instance.
	 * @param array $request
	 */
	public function __construct(array $request)
	{
		$this->request = new Request($request);
		$this->service = new UserContactDetailsService();
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		$this->service->store($this->request);
	}
}