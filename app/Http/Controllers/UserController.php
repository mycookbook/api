<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Jobs\TriggerEmailVerificationProcess;
use App\Models\EmailVerification;
use App\Models\Following;
use App\Models\User;
use App\Models\UserFeedback;
use App\Services\TikTok\AccessToken;
use App\Services\TikTok\HttpRequestRunner;
use App\Services\TikTok\Videos;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class UserController
 */
class UserController extends Controller
{
    protected UserService $service;

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

    public function followUser(Request $request)
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {
            if ($toFollow = $request->get('toFollow')) {
                $userToFollow = $this->service->findWhere($toFollow)->first();

                if ($userToFollow instanceof User) {
                    /** @var User $user */
                    $user = User::findOrFail($user->getKey());

                    if (!$user->isAlreadyFollowing($userToFollow)) {
                        $followingsCount = $user->following;
                        $followingsCount += 1;

                        $user->update(['following' => $followingsCount]);

                        $following = new Following([
                            'follower_id' => $user->getKey(),
                            'following' => $userToFollow->getKey()
                        ]);

                        $following->save();

                        return response()->json($this->getWhoToFollowData($user), Response::HTTP_OK);
                    }

                    return response()->noContent(Response::HTTP_OK);
                }
            }

            return response()->json(['error', 'Bad request.'], Response::HTTP_BAD_REQUEST);
        }

        return $this->unauthorizedResponse();
    }

    /**
     * TODO: Implement this
     * The logic to get who to follow is undecided yet
     * For now, this just returns the latest five unfollowed users in the database
     */
    public function getWhoToFollow()
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {
           return $this->getWhoToFollowData($user);
        }

        return $this->unauthorizedResponse();
    }

    private function getWhoToFollowData(User $user)
    {
        $followings = Following::where(['follower_id' => $user->getKey()])->pluck('following')->toArray();
        $followings[] = $user->getKey();

        $latest = User::whereNotIn('id', $followings)->latest()->take(3)->get();

        return $latest->map(function($user) {
            return [
                'followers' => $user->following,
                'author' => $user->name,
                'avatar' => $user->avatar,
                'handle' => $user->name_slug
            ];
        });
    }

    public function addFeedback(Request $request)
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {

            try {
                $hasRespondedAlready = UserFeedback::where(['user_id' => $user->getKey(), 'type' => 'feedback']);

                if (collect($hasRespondedAlready->pluck('response')->toArray())->isEmpty()) {
                    $userFeedback = new UserFeedback([
                        'user_id' => $user->getKey(),
                        'type' => 'feedback',
                        'response' =>  $request->get('choice', 'still-thinking')
                    ]);

                    return response()->json(['success' => $userFeedback->save()]);
                }

                return response()->json([
                    'success' => $hasRespondedAlready->first()->update([
                        'response' =>  $request->get('choice', 'still-thinking')
                    ])
                ]);
            } catch (ApiException $exception){
                Log::debug('error creating user feedback', ['exception' => $exception]);
                return response()->json(['error', 'There was an error processing this request. Please try again later.'], $exception->getCode());
            }
        }

        return $this->unauthorizedResponse();
    }

    public function listVideos(HttpRequestRunner $requestRunner)
    {
        /** @phpstan-ignore-next-line */
        if ($user = JWTAuth::parseToken()->user()) {
            $tikTokUser = $user->getTikTokUser();
            $errors = [
                'videos_count' => 0,
                'videos' => []
            ];

            if ($tikTokUser === null) {
                return [
                    'data' => array_merge($errors, ['error' => [
                        "You don't have any tiktok videos,",
                        "Or your account is marked private,",
                        "Or you denied cookbookshq access to your videos."
                    ]])
                ];
            }

            try {
                $response = $requestRunner(['code' => $tikTokUser->code], false, new AccessToken(), new Videos());

                return response()->json([
                    'data' => [
                        'videos_count' => 0,
                        'videos' => $response->getContents()['videos']
                    ]
                ]);
            } catch (\Exception $exception) {
                Log::debug("Error listing tiktok user videos", ['exception' => $exception]);

                return [
                    'data' => array_merge(
                        $errors,
                        ['server_error' => 'There was a problem listing your videos. Please try again later.']
                    )
                ];
            }
        }

        return $this->unauthorizedResponse();
    }
}
