<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Jobs\TriggerEmailVerificationProcess;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->service->store($request);
    }

    /**
     * Get one user
     *
     * @param mixed $username username
     * @throws \App\Exceptions\CookbookModelNotFoundException
     */
    public function show($username)
    {
        return $this->service->show($username);
    }

    /**
     * @param $username
     * @param UserUpdateRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    public function update($username, UserUpdateRequest $request)
    {
        if ($request->all()) {
            $request->merge(['username']);

            return $this->service->update($request, $username);
        }

        return response()->json([
            'message' => 'nothing to update.',
        ]);
    }

    /**
     * Email Verification
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function verifyEmail($token)
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
                    'is_verified' => Carbon::now(),
                ]);

                return response()->json(null, Response::HTTP_NO_CONTENT);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), Response::HTTP_CONFLICT);
        }
    }

    /**
     * @param $token
     *
     * @throws \Exception
     */
    public function resend($token)
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
