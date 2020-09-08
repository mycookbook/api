<?php

namespace App\Http\Controllers;

use App\EmailVerification;
use App\Services\UserService;
use App\Http\Controllers\Requests\User\StoreRequest;
use App\Http\Controllers\Requests\User\UpdateRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

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
	 * @param int $username username
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
		$email = Crypt::decryptString($token);

		try {
			$user = User::where('email', $email);
			$verification = EmailVerification::where('user_id', $user->id);
			$verification->update([
				'is_verified' => Carbon::now()
			]);

			return response()->json(null, Response::HTTP_NO_CONTENT);

		} catch (\Exception $e) {
			return response()->json('Something went wrong. Please try again later.', Response::HTTP_CONFLICT);
		}
	}
}
