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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Ip2location\IP2LocationLaravel\Facade\IP2LocationLaravel;

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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginViaMagicLink(Request $request)
    {
        return response()->json([
            'error' => [
                'message' => 'This singin method is limited to ONLY authorized users. Please login with TikTok instead'
            ]
        ], Response::HTTP_UNAUTHORIZED);


//        $records = IP2LocationLaravel::get($request->getClientIp(), 'bin');
//
//        dd($records);
        //inspect the request
        //check location details
        //if not found, respond with request: user_email
        //if user_email in request,
        //validate email against location
        //if validation fails, respond with 401 and message
        //if validation suceeds, login and respond with token
        //consoder invalidating user's old tokens
        //if location details found, dont ask for user email
        //continue to log user in and respond with new token
        //the new token responses are actually redirects
        //also consider when email is in allowed list but the location is not recognized
    }

    /**
     * TikTok does not give you the user's email due to privacy policy
     *
     * @throws GuzzleException
     */
    public function tikTokHandleCallback(Request $request, Client $client, UserService $service)
    {
        $code = $request->get('code');
        $errCode = $request->get('errCode');

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
                throw new \Exception(json_encode($decoded));
            }

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
        } catch (\Exception $e) {
            return redirect('https://web.cookbookshq.com/#/errors/?m=Tiktok is having a hard time processing this request, please try again.');
//            $message = $e->getMessage();
//
//            if ($this->isJson($message)) {
//                $message = json_decode($message, true);
//            }
//
//            return response()->json(
//                [
//                    'auth_error' => $message,
//                ], 400
//            );
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
