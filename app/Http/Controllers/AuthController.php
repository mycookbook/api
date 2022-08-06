<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    public function tikTokHandleCallback(Request $request, Client $client)
    {
        // fetch access token using code
        $code = $request->get("code");

        try {
            $response = $client->request('POST',
                'https://open-api.tiktok.com/oauth/access_token/',
                [
                    'form_params' => [
                        'client_key' => 'awzqdaho7oawcchp',
                        'client_secret' => '5376fb91489d66bd64072222b454740a',
                        'code' => $code,
                        'grant_type' => 'authorization_code'
                    ]
                ]
            );

            $decoded = json_decode($response->getBody()->getContents(), true);

            if ($decoded["message"] === 'error') {
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
                            'fields' => ["open_id", "avatar_url", "display_name", "avatar_url_100"]
                        ]
                    ]
                );

                //{"data":{"user":{"open_id":"a93c026e-dd03-4e99-98d9-a9d68a61b42c","display_name":"CookbooksHQ"}},"error":{"code":0,"message":""}}

                $userInfo = json_decode($userInfoResponse->getBody()->getContents(), true);

                $to = "https://web.cookbookshq.com/#/tiktok/" . http_build_query(["code" => $userInfo['data']['user']['open_id']]);

                return redirect($to);
            }
        } catch(\Exception $exception) {
            dd($exception->getMessage());
        } catch (GuzzleException $e) {
            dd($exception->getMessage());
        }
    }
}
