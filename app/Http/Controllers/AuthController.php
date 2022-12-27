<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\SignInRequest;
use App\Models\Location;
use App\Models\User;
use App\Services\AuthService;
use App\Services\LocationService;
use App\Services\UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function loginViaMagicLink(Request $request, LocationService $locationService)
    {
        try {
            $location = $locationService->getLocation($request);

            if ($requestUserEmail = $request->get("email")) {
                $locationUserEmail = $location->getUser()->email;

                if ($locationUserEmail != $requestUserEmail) {
                    $locationService->setErrorResponse([
                        'error' => [
                            'message' => 'You are not authorized.'
                        ]
                    ]);

                    throw new ApiException();
                } else {
                    $credentials = ['email' => $requestUserEmail, 'password' => 'fakePass'];

                    $token = Auth::attempt($credentials);

                    $to = 'https://web.cookbookshq.com/#/?' . http_build_query([
                            'token' => $token,
                            '_d' => $location->getUser()->getSlug(),
                        ]);

                    return redirect($to);
                }
            } else {
                $locationService->setErrorResponse([
                    'error' => [
                        'message' => "Please provide your email."
                    ]
                ]);

                $redirectTo = 'https://web.cookbookshq.com/#/errors/?' . http_build_query($locationService->getErrors());
                return redirect($redirectTo);
            }
        } catch (\Throwable $e) {
            $redirectTo = 'https://web.cookbookshq.com/#/errors/?' . http_build_query($locationService->getErrors());
            return redirect($redirectTo);
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
                return redirect('https://web.cookbookshq.com');
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

                $to = 'https://web.cookbookshq.com/#/tiktok/?' . http_build_query([
                        'token' => $token,
                        '_d' => $user->getSlug(),
                    ]);

                return redirect($to);
            } else {
                return redirect('https://web.cookbookshq.com/#/errors/?m=Hey, it looks like your tiktok account is Private. Please login using a public account.');
            }
        } catch (\Exception $e) {
            Log::debug('There was an error', [
                'error' => $e->getMessage(),
            ]);

            return redirect('https://web.cookbookshq.com/#/errors/?m=Tiktok is having a hard time processing this request, please try again.');
        }
    }

    /**
     * @param $json
     * @return bool
     */
    private function isJson($json)
    {
        $result = json_decode($json);

        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }

        return false;
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
