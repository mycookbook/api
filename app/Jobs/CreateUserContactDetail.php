<?php

namespace App\Jobs;

use App\Services\UserContactDetailsService;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
		$this->service = new UserContactDetailsService($this->request);
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		$this->service->store($this->request);
		Log::info('Email job queued and executed...');
	}
}