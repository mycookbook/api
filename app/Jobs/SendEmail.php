<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * Create a new job instance.
	 * @return void
	 */
	public function __construct()
	{
		Log::info('sending Email');
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		Log::info('Email job queued and executed...');
	}
}