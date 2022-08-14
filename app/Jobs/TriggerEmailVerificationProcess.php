<?php

namespace App\Jobs;

use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TriggerEmailVerificationProcess implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $userId;

    /**
     * TriggerEmailVerificationProcess constructor.
     *
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->user = User::find($this->userId);

        if (! $this->user) {
            Log::info('This user does not exist', ['user_id' => $this->userId]);
        } else {
            $user_email_verification_exist = EmailVerification::where('user_id', $this->userId)->get()->first();

            if ($user_email_verification_exist) {
                //update

                $new_token = $this->getToken($this->getPayload());

                $user_email_verification_exist->update([
                    'token' => $new_token,
                    'is_verified' => null,
                ]);

                if (! $user_email_verification_exist->save()) {
                    Log::info('Existing email verification failed to update: ', [$user_email_verification_exist]);
                }
            } else {
                //create

                $token = $this->getToken($this->getPayload());

                $new_email_verification = new EmailVerification([
                    'user_id' => $this->user->id,
                    'token' => $token,
                ]);

                if (! $new_email_verification->save()) {
                    Log::info('Failed to save new email verification: ', [$new_email_verification]);
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getPayload()
    {
        return [
            'email' => $this->user->email,
            'user_id' => $this->user->id,
            'secret' => env('CRYPT_SECRET'),
        ];
    }

    /**
     * @param $payload
     * @return string
     */
    protected function getToken($payload)
    {
        return Crypt::encrypt($payload);
    }
}
