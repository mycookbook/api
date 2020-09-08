<?php

namespace App\Jobs;

use App\User;
use Carbon\Carbon;
use App\EmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerEmailVerificationProcess implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	protected $token;
	protected $user;

	/**
	 * TriggerEmailVerificationProcess constructor.
	 * @param $userId
	 */
	public function __construct($userId)
	{
		$this->user = $this->getUser($userId)->get()->first();
		$user_email = $this->user->email;
		$this->token = Crypt::encryptString($user_email);
	}

	/**
	 * @throws \Exception
	 */
	public function handle()
	{
		$email_verification = new EmailVerification([
			'user_id' => $this->user->id,
			'token' => $this->token,
			'is_verified' => Carbon::now()
		]);

		if (!$email_verification->save()) {
			Log::info('Failed to save: ', [$email_verification]);
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function getUser($id)
	{
		return User::findOrFail($id);
	}
}
