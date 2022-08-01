<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tymon\JWTAuth\JWTAuth;
use App\Services\AuthService;
use App\Http\Controllers\Requests\Auth\SignInRequest;

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
	 * Authenticate the user with AuthService
	 *
	 * @param SignInRequest $request
	 * @param JWTAuth $jwt
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login(SignInRequest $request, JWTAuth $jwt): \Illuminate\Http\JsonResponse
	{
		return $this->service->login($request->getParams(), $jwt);
	}

    /**
     * @param Request $request
     * @param JWTAuth $jwt
     * @param Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function tiktokLogin(Request $request, JWTAuth $jwt, Client $client): \Illuminate\Http\JsonResponse
    {
        // fetch access token using code
        $code = $request->get("code");
//        dd($code);

        $response = $client->post(
            'https://open-api.tiktok.com/oauth/access_token/',
            [
                'client_key' => '',
                'client_secret' => '',
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        );

//        bPauvp6wDcJ0Vv1IBhhsGvn96ZUK3tHL4X8ZYqo5I72N3OS1wAqisWk-Jpzr1zzkpVBTC6YwDvJtpejnwa3FAs6PBY6WeT0bXpMNusfXk_E%2a2%214623
//&scopes=user.info.basic,video.list&state=ficndb24cdp#/

        if ($response->getStatusCode() == 200) {
            $responseBody = json_decode($response->getBody()->getContents(), true);

            $userInfoResponse = $client->post(
                'https://open-api.tiktok.com/user/info/',
                [
                    'open_id' => $responseBody['open_id'],
                    'access_token' => $responseBody['access_token'],
                    'fields' => '["open_id", "avatar", "display_name"]'
                ]
            );

            if ($userInfoResponse->getStatusCode() == 200) {
                return response()->json(
                    [
                        'access_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                    ], ResponseAlias::HTTP_OK
                );
            } else {
                dd('error');
            }
        }

        // grab access_token from response
        // call the userInfo endpoint using this access token
        // grab open_id and display_name from the response
        // construct an email with the above combination
        // if a user exists with that email, expire all user jwt tokens and generate a new jwt token
        // else create new user with that email and generate new jwt token
//        return $this->service->socialAuth($request, $jwt);

        return response()->json(
            [
                'error' => 'error from tiktok',
            ], 400
        );
    }
}
