<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(['data' => $this->service->index()]);
    }

    /**
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        return $this->service->store($request) ? $this->successResponse(
            [
                'response' => [
                    'created' => true,
                    'data' => [],
                    'status' => 'success'
                ]
            ],
            ResponseAlias::HTTP_CREATED
        ) : $this->errorResponse(['error' => 'There was an error processing this request. Please try again.']);
    }

    public function show($username)
    {
        return $this->successResponse(['data' => ['user' => $this->service->show($username)]]);
    }

    public function update($username, UserUpdateRequest $request)
    {
        if ($request->all()) {
            $request->merge(['username' => $username]);

            if ($this->service->update($request, $username)) {
                return $this->successResponse(['updated' => true, 'status' => 'success']);
            }

            return $this->errorResponse(['updated' => false, 'status' => 'failed']);
        }

        return response()->json(['message' => 'nothing to update.',]);
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

                        return response()->json($this->getWhoToFollowData($user), ResponseAlias::HTTP_OK);
                    }

                    return response()->noContent(ResponseAlias::HTTP_OK);
                }
            }

            return response()->json(['error', 'Bad request.'], ResponseAlias::HTTP_BAD_REQUEST);
        }

        return $this->unauthorizedResponse();
    }

    /**
     * TODO: Implement this
     * The logic to get who to follow is undecided yet
     * For now, this just returns the latest five unfollowed users in the database
     */
    public function getWhoToFollow(Request $request, JWT $jwtAuth)
    {
        return ($jwtAuth->parseToken()->check()) ?
            $this->getWhoToFollowData($request->user()) :
            $this->unauthorizedResponse();
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
