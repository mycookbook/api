<?php

namespace App\Traits;

use Illuminate\Auth\Notifications\VerifyEmail;

trait CookbookUserMustVerifyEmail
{
	/**
	 * Determine if the user has verified their email address.
	 *
	 * @return bool
	 */
	public function hasVerifiedEmail(): bool
	{
		return !is_null($this->email_verified);
	}

	/**
	 * Mark the given user's email as verified.
	 *
	 * @return bool
	 */
	public function markEmailAsVerified()
	{
		return $this->forceFill([
			'email_verified' => $this->freshTimestamp(),
		])->save();
	}

	/**
	 * Send the email verification notification.
	 *
	 * @return void
	 */
	public function sendEmailVerificationNotification()
	{
		$this->notify(new VerifyEmail);
	}

	/**
	 * Get the email address that should be used for verification.
	 *
	 * @return string
	 */
	public function getEmailForVerification()
	{
		return $this->email;
	}
}
