<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\OtpWasGenerated;
use App\Models\Following;
use App\Models\User;
use App\Models\UserFeedback;
use App\Services\TikTok\AccessToken;
use App\Services\TikTok\HttpRequestRunner;
use App\Services\TikTok\Videos;
use App\Services\UserService;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function store(UserStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->service->store($request);
    }

    public function show($username)
    {
        return $this->service->show($username);
    }

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
                Log::debug(
                    "Error listing tiktok user videos",
                    ['exception' => $exception]
                );

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

    public function generateOtp(Request $request, Otp $otp)
    {
        $identifier = (string) $request->get('identifier');

        $token = $otp->generate(
            $identifier,
            config('services.otp.digits'),
            config('services.otp.validity')
        );

        try {
            Mail::to($identifier)->send(new OtpWasGenerated($token->token));
        } catch (\Exception $exception) {
            Log::debug(
                'Error sending OTP email',
                [
                    'identifier' => $identifier,
                    'errorMsg' => $exception->getMessage()
                ]
            );

            return $this->errorResponse(['message' => 'There was an error processing this request. Please try again.']);
        }
    }

    public function validateOtp(Request $request, Otp $otp): object
    {
        $identifier = (string) $request->get('identifier');
        $token = (string) $request->get('token');

        return $otp->validate($identifier, $token);
    }
}
