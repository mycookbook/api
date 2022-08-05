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
                $userInfoResponse = $client->post(
                    'https://open-api.tiktok.com/user/info/',
                    [
                        'open_id' => $decoded['open_id'],
                        'access_token' => $decoded['access_token'],
                        'fields' => '["open_id", "avatar", "display_name"]'
                    ]
                );

                return response()->json(
                    [
                        'access_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                    ], ResponseAlias::HTTP_OK
                );
            }

            // grab access_token from response
            // call the userInfo endpoint using this access token
            // grab open_id and display_name from the response
            // construct an email with the above combination
            // if a user exists with that email, expire all user jwt tokens and generate a new jwt token
            // else create new user with that email and generate new jwt token
//        return $this->service->socialAuth($request, $jwt);
        } catch(\Exception $exception) {
            dd($exception->getMessage());
        } catch (GuzzleException $e) {
            dd($exception->getMessage());
        }
    }
}
