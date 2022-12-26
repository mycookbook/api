<?php

namespace App\Http\Controllers;

use App\Dtos\TikTok;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
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
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function socialAuth(Request $request)
    {
        $provider = $request->route()->getAction()["provider"];

        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function socialAuthCallbackHandler(Request $request)
    {
        try {
            $provider = $request->route()->getAction()["provider"];

            return response()->json([
                'titok' => $provider
            ]);

//            $user = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * TikTok does not give you the user's email due to privacy policy
     *
     * @throws GuzzleException
     */
    public function tikTokHandleCallback(Request $request, Client $client, UserService $service)
    {
        $code = $request->get('code');

        if ($request->get('errCode') == TikTok::USER_CANCELLED_CODE) {
            return redirect(config('services.web.base_url'));
        }

        try {
            $response = $client->request('POST',
                config('services.tiktok.open_api.access_token_uri'),
                [
                    'form_params' => [
                        'client_key' => config('services.tiktok.client_id'),
                        'client_secret' => config('services.tiktok.client_secret'),
                        'code' => $code,
                        'grant_type' => config('services.tiktok.open_api.grant_type'),
                    ],
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            if ($decoded['message'] === 'error') {
                $decoded['code'] = $code;

                return response()->json(
                    [
                        'error_' => $decoded,
                    ], 400
                );
            } else {
                $userInfoResponse = $client->request('POST',
                    config('services.tiktok.open_api.user_info_uri'),
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
                            'password' => config('services.tiktok.user_password'),
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
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'auth_error' => $e->getMessage(),
                ], 400
            );
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
