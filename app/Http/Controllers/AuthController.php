<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\Auth\SignInRequest;
use App\Services\AuthService;
use App\Services\UserService;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /**
     * @param  AuthService  $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Authenticate the user with AuthService
     *
     * @param  SignInRequest  $request
     * @param  JWTAuth  $jwt
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(SignInRequest $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
    {
        return $this->service->login($request->getParams(), $jwt);
    }

    /**
     * TikTok does not give you the user's email due to privacy policy
     *
     * @param  Request  $request
     * @param  Client  $client
     * @param  UserService  $service
     * @param  JWTAuth  $jwt
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector|void
     *
     * @throws GuzzleException
     */
    public function tikTokHandleCallback(Request $request, Client $client, UserService $service, JWTAuth $jwt)
    {
        $code = $request->get('code');

        try {
            $response = $client->request('POST',
                'https://open-api.tiktok.com/oauth/access_token/',
                [
                    'form_params' => [
                        'client_key' => 'awzqdaho7oawcchp',
                        'client_secret' => '5376fb91489d66bd64072222b454740a',
                        'code' => $code,
                        'grant_type' => 'authorization_code',
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
                    'https://open-api.tiktok.com/user/info/',
                    [
                        'json' => [
                            'open_id' => $decoded['data']['open_id'],
                            'access_token' => $decoded['data']['access_token'],
                            'fields' => ['open_id', 'avatar_url', 'display_name', 'avatar_url_100'],
                        ],
                    ]
                );

                //{"data":{"user":{"open_id":"a93c026e-dd03-4e99-98d9-a9d68a61b42c","display_name":"CookbooksHQ"}},"error":{"code":0,"message":""}}

                $userInfo = json_decode($userInfoResponse->getBody()->getContents(), true);

                if (! empty($userInfo['data']['user'])) {
                    $tiktokEmail = $userInfo['data']['user']['open_id'].'@tiktok.com';

                    $user = User::where(['email' => $tiktokEmail])->first();

                    if (! $user instanceof User) {
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

                    if (! $token = $jwt->attempt($credentials)) {
                        return redirect('https://web.cookbookshq.com/#/errors/?m=there was an error processing this request, please try again.');
                    }

                    $to = 'https://web.cookbookshq.com/#/tiktok/?'.http_build_query([
                        'code' => $token,
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
}
