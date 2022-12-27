<?php

namespace App\Services;

use App\Exceptions\ApiException;
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
     * @return bool|Location
     * @throws ApiException
     */
    public function getLocation(Request $request): ?Location
    {
        if (!$location = Location::find($request->ipinfo->ip)) {
            $this->errMessage = [
                'error' => [
                    'message' => 'This singin method is limited to ONLY authorized users. Please login with TikTok instead.'
                ]
            ];

            throw new ApiException();
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
