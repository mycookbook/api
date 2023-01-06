<?php

namespace App\Services;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationService
{
    /**
     * @var array $errMessage
     */
    protected array $errMessage = [];

    /**
     * @param Request $request
     * @return Location|null
     */
    public function getLocation(Request $request): ?Location
    {
        dd($request->ipinfo->ip);
        $location = Location::where(['ip' => $request->ipinfo->ip])->first();

        if (!$location) {
            $this->errMessage = [
                'error' => [
                    'message' => 'This singin method is limited to ONLY authorized users. Please login with TikTok instead.'
                ]
            ];
        }

        return $location;
    }

    /**
     * @param string $email
     * @return Location|null
     */
    public static function getLocationByUserEmail(string $email): ?Location
    {
        $location = null;

        $user_emails = User::where(['email' => $email])->pluck('id', 'email')->toArray();

        if (array_key_exists($email, $user_emails)) {
            $location = Location::where(['user_id' => $user_emails[$email]])->first();
        }

        return $location;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errMessage;
    }

    /**
     * @param array $error
     * @return LocationService
     */
    public function setErrorResponse($error = [])
    {
        $this->errMessage = $error;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return User::findOrFail($this->user_id);
    }
}
