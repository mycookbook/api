<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\TikTokUserDto;
use App\Events\TikTokUserIsAuthenticated;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\LocationService;
use App\Services\UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    protected AuthService $service;

    public const TIKTOK_CANCELLATION_CODE = "-2";

    /**
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param SignInRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(SignInRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->service->login($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function logout()
    {
        return $this->service->logout();
    }

    /**
     * @param Request $request
     * @param LocationService $locationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginViaMagicLink(Request $request, LocationService $locationService)
    {
        try {
            $location = $locationService->getLocation($request);
            $userEmailFromRequest = $request->get("email");

            if (!$location && !$userEmailFromRequest) {
                return response()->json([
                    'action_required' => true,
                    'required' => [
                        'email' => 'Looks like this is your first time signing in with magiclink! Kindly provide your registered email for verification.',
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$location && $userEmailFromRequest) {
                $location = LocationService::getLocationByUserEmail($userEmailFromRequest);

                if (!$location) {
                    $locationService->setErrorResponse([
                        'error' => [
                            'message' => 'This feature is limited to ONLY authorized users. Please login with TikTok instead.'
                        ]
                    ]);

                    return response()->json($locationService->getErrors(), Response::HTTP_UNAUTHORIZED);
                }

                $locationUserEmail = $location->getUser()->email;

                if ($locationUserEmail != $userEmailFromRequest) {
                    $locationService->setErrorResponse([
                        'error' => [
                            'message' => 'This feature is limited to ONLY authorized users. Please login with TikTok instead.'
                        ]
                    ]);

                    return response()->json($locationService->getErrors(), Response::HTTP_UNAUTHORIZED);
                } else {
                    $location->update([
                        'ip' => $request->ipinfo->ip,
                        'city' => $request->ipinfo->city ?? '',
                        'country' => $request->ipinfo->country ?? '',
                        'timezone' => $request->ipinfo->timezone ?? 'America/Toronto'
                    ]);

                    return response()->json([
                        'token' => Auth::attempt([
                            'email' => $userEmailFromRequest,
                            'password' => config('services.faker.pass')
                        ]),
                        '_d' => $location->getUser()->getSlug()
                    ]);
                }
            }

            return response()->json([
                'token' => Auth::attempt([
                    'email' => $location->getUser()->email,
                    'password' => config('services.faker.pass')
                ]),
                '_d' => $location->getUser()->getSlug()
            ]);
        } catch (\Throwable $e) {
            $m = array_merge($locationService->getErrors(), [$e->getMessage()]);

            return response()->json($m, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * TikTok does not give you the user's email due to privacy policy
     *
     * @throws GuzzleException
     */
    public function tikTokHandleCallback(Request $request, Client $client, UserService $service)
    {
        try {
            $code = $request->get('code');
            $errCode = $request->get('errCode');

            if ($errCode === self::TIKTOK_CANCELLATION_CODE) {
                return redirect('https://web.cookbookshq.com/#/signin');
            }

            $response = $client->request('POST',
                'https://open-api.tiktok.com/oauth/access_token/',
                [
                    'form_params' => [
                        'client_key' => config('services.tiktok.client_id'),
                        'client_secret' => config('services.tiktok.client_secret'),
                        'code' => $code,
                        'grant_type' => 'authorization_code',
                    ],
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            if ($decoded['message'] === 'error') {
                throw new \Exception(json_encode($decoded));
            }

            $userInfoResponse = $client->request('POST',
                'https://open-api.tiktok.com/user/info/',
                [
                    'json' => [
                        'open_id' => $decoded['data']['open_id'],
                        'access_token' => $decoded['data']['access_token'],
                        'fields' => ['open_id', 'avatar_url', 'display_name', 'avatar_url_100'],
                    ],
                ]
            );

            $userInfo = json_decode($userInfoResponse->getBody()->getContents(), true);
            dd($userInfo);

            if (!empty($userInfo['data']['user'])) {
                $tiktokEmail = $userInfo['data']['user']['open_id'] . '@tiktok.com';

                $user = User::where(['email' => $tiktokEmail])->first();

                if (!$user instanceof User) {
                    $response = $service->store(new Request([
                        'name' => $userInfo['data']['user']['display_name'],
                        'email' => $tiktokEmail,
                        'password' => 'fakePass',
                    ]));

                    $decoded = json_decode($response->getContent(), true);
                    $data = $decoded['response']['data'];
                    $user = User::where(['email' => $data['email']])->first();
                }

                $user->update([
                    'avatar' => $userInfo['data']['user']['avatar_url'],
                    'pronouns' => 'They/Them',
                ]);

                $credentials = [
                    'email' => $user->email,
                    'password' => 'fakePass',
                ];

                if (!$token = Auth::attempt($credentials)) {
                    return redirect('https://web.cookbookshq.com/#/errors/?m=there was an error processing this request, please try again.');
                }

                TikTokUserIsAuthenticated::dispatch(new TikTokUserDto(
                    $user->getKey(),
                    $userInfo['data']['user']['open_id'],
                    $code,
                    $userInfo['data']['user']['is_verified'],
                    $userInfo['data']['user']['profile_deep_link'],
                    $userInfo['data']['user']['bio_description'],
                    $userInfo['data']['user']['bio_description'],
                    $userInfo['data']['user']['display_name'],
                    $userInfo['data']['user']['avatar_large_url'],
                    $userInfo['data']['user']['avatar_url_100'],
                    $userInfo['data']['user']['union_id'],
                    $userInfo['data']['user']['video_count']
                ));

                $to = 'https://web.cookbookshq.com/#/tiktok/?' . http_build_query([
                        'token' => $token,
                        '_d' => $user->getSlug(),
                    ]);

                return redirect($to);
            } else {
                return redirect('https://web.cookbookshq.com/#/errors/?m=Hey, it looks like your tiktok account is Private. Please login using a public account.');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::debug('There was an error', [
                'error' => $e->getMessage(),
            ]);

            return redirect('https://web.cookbookshq.com/#/errors/?m=Tiktok is having a hard time processing this request, please try again.');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function validateToken(Request $request)
    {
        if (!$request->bearerToken() || !Auth::check()) {
            throw new \Tymon\JWTAuth\Exceptions\JWTException('Expired or Tnvalid token.');
        }

        return response()->json(
            [
                'validated' => true,
            ]
        );
    }
}
