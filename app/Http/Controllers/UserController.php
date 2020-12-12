<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\EmailVerification;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Jobs\TriggerEmailVerificationProcess;
use App\Http\Controllers\Requests\User\StoreRequest;
use App\Http\Controllers\Requests\User\UpdateRequest;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @param \App\Services\UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Get all users from the database
     */
    public function index()
    {
        return $this->service->index();
    }

	/**
	 * Create new user
	 *
	 * @param \App\Http\Controllers\Requests\User\StoreRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function store(StoreRequest $request)
    {
        return $this->service->store($request->getParams());
    }

	/**
	 * Get one user
	 *
	 * @param mixed $username username
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
    public function show($username)
    {
        return $this->service->show($username);
    }

	/**
	 * Implement a full/partial update
	 *
	 * @param \App\Http\Controllers\Requests\User\UpdateRequest $request
	 * @param string $userId userName
	 *
	 * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
	 */
	public function update(UpdateRequest $request, string $userId)
	{
		return $this->service->update($request->getParams(), $userId);
	}

	/**
	 * Email Verification
	 *
	 * @param Request $request
	 * @param $token
	 */
	public function verifyEmail(Request $request, $token)
	{
		$payload = Crypt::decrypt($token);

		try {
			if ($payload['secret'] != env('CRYPT_SECRET')) { //one more layer of scrutiny
				Log::info('Invalid secret provided for verifying this email', ['user_id' => $payload['user_id'], 'email' => $payload['email']]);
				throw new \Exception('There was a problem processing this request. Please try again later.');
			}

			$user = User::findOrFail($payload['user_id']);

			if ($user) {
				$verification = EmailVerification::where('user_id', $payload['user_id']);
				$verification->update([
					'is_verified' => Carbon::now()
				]);

				return response()->json(null, Response::HTTP_NO_CONTENT);
			}
		} catch (\Exception $e) {
			return response()->json($e->getMessage(), Response::HTTP_CONFLICT);
		}
	}

	/**
	 * @param Request $request
	 * @param $token
	 * @throws \Exception
	 */
	public function resend(Request $request, $token)
	{
		$payload = Crypt::decrypt($token);

		if ($payload['secret'] != env('CRYPT_SECRET')) { //one more layer of scrutiny
			Log::info('Invalid secret provided for resending email verification', ['user_id' => $payload['user_id'], 'email' => $payload['email']]);
			throw new \Exception('There was a problem processing this request. Please try again later.');
		} else {
			dispatch(new TriggerEmailVerificationProcess($payload['user_id']));
		}
	}
}
