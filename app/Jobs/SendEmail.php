<?php

namespace App\Jobs;

use App\Mail\UserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
		Log::info('Hellur World');
	}

	/**
	 * Execute the job.
	 * @return void
	 */
	public function handle()
	{
		$userCreated = new UserCreated();
		$userCreated->from('okosunuzflorence@gmail.com', 'Hellur its me');
		$userCreated->to(['okosunuzflorence@gmail.com']);
		$mail = Mail::to($userCreated);
		$mail->send($this);
		Log::info('The end...');
	}
}