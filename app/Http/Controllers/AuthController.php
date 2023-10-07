<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\TikTokUserDto;
use App\Events\TikTokUserIsAuthenticated;
use App\Http\Clients\TikTokHttpClient;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\LocationService;
use App\Services\UserService;
use App\Utils\UriHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tymon\JWTAuth\Exceptions\JWTException;

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

    public function login(SignInRequest $request): \Illuminate\Http\JsonResponse
    {
        if (!$token = $this->service->login($request)) {
            return response()->json(
                [
                    'Not found or Invalid Credentials.',
                ], ResponseAlias::HTTP_NOT_FOUND
            );
        }

        return $this->successResponse(['token' => $token]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function logout()
    {
        return $this->service->logout() ?
            $this->noContentResponse() :
            $this->errorResponse(['Not found or Invalid Credentials.']);
    }

    /**
     * @param Request $request
     * @param LocationService $locationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginViaMagicLink(Request $request, LocationService $locationService)
    {
        $location = $locationService->getLocation($request);
        $userEmailFromRequest = $request->get("email");

        if (!$location && !$userEmailFromRequest) {
            return response()->json([
                'action_required' => true,
                'required' => [
                    'email' => 'Looks like this is your first time signing in with magiclink! Kindly provide your registered email for verification.',
                ]
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            if (!$location && $userEmailFromRequest) {
                $location = LocationService::getLocationByUserEmail($userEmailFromRequest);

                if (!$location) {
                    $locationService->setErrorResponse([
                        'error' => [
                            'message' => 'This feature is limited to ONLY authorized users. Please login with TikTok instead.'
                        ]
                    ]);

                    return response()->json($locationService->getErrors(), ResponseAlias::HTTP_UNAUTHORIZED);
                }

                $locationUserEmail = $location->getUser()->email;

                if ($locationUserEmail != $userEmailFromRequest) {
                    $locationService->setErrorResponse([
                        'error' => [
                            'message' => 'This feature is limited to ONLY authorized users. Please login with TikTok instead.'
                        ]
                    ]);

                    return response()->json($locationService->getErrors(), ResponseAlias::HTTP_UNAUTHORIZED);
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

            return response()->json($m, ResponseAlias::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * TikTok does not give you the user's email due to privacy policy
     *
     * @throws GuzzleException
     */
    public function tikTokHandleCallback(Request $request, TikTokHttpClient $httpClient, UserService $service)
    {
        $code = $request->get('code');
        $errCode = $request->get('errCode');

        if ($errCode === self::TIKTOK_CANCELLATION_CODE) {
            return redirect('https://stg.cookbookshq.com/#/signin');
        }

        try {
            $decoded = $httpClient->getAccessToken($code);
            $message = Arr::get($decoded, 'message');
            $open_id = Arr::get($decoded, 'data.open_id');
            $access_token = Arr::get($decoded, 'data.access_token');

            if ($message === 'error') {
                throw new \Exception(json_encode($decoded));
            }

            $userInfo = $httpClient->getUserInfo($open_id, $access_token);

            if (!empty($userInfo['data']['user'])) {
                $open_id = Arr::get($userInfo, 'data.user.open_id');
                $displayName = Arr::get($userInfo, 'data.user.display_name');
                $tiktokEmail = $open_id . '@tiktok.com';
                $user = User::where(['email' => $tiktokEmail])->first();

                if (!$user instanceof User) {
                    $response = $service->store(new Request([
                        'name' => $displayName,
                        'email' => $tiktokEmail,
                        'password' => config('services.tiktok.users.secret_pass'),
                    ]));

                    $decoded = json_decode($response->getContent(), true);
                    $data = Arr::get($decoded, 'response.data');
                    $user = User::where(['email' => Arr::get($data, 'email')])->first();
                }

                $user->update([
                    'avatar' => Arr::get($userInfo, 'data.user.avatar_url'),
                    'pronouns' => 'They/Them',
                ]);

                $credentials = [
                    'email' => $user->email,
                    'password' => config('services.tiktok.users.secret_pass')
                ];

                if (!$token = Auth::attempt($credentials)) {
                    return UriHelper::redirectToUrl(
                        UriHelper::buildHttpQuery(
                            'errors',
                            ['m' => Lang::get('errors.generic')]
                        )
                    );
                }

                TikTokUserIsAuthenticated::dispatch(new TikTokUserDto(
                    $user->getKey(),
                    Arr::get($userInfo, 'data.user.open_id'),
                    Arr::get($decoded, 'data.access_token'),
                    Arr::get($userInfo, 'data.user.is_verified'),
                    Arr::get($userInfo, 'data.user.profile_deep_link'),
                    Arr::get($userInfo, 'data.user.bio_description'),
                    Arr::get($userInfo, 'data.user.display_name'),
                    Arr::get($userInfo, 'data.user.avatar_large_url'),
                    Arr::get($userInfo, 'data.user.avatar_url_100'),
                    Arr::get($userInfo, 'data.user.avatar_url'),
                    Arr::get($userInfo, 'data.user.union_id'),
                    Arr::get($userInfo, 'data.user.video_count')
                ));

                $to = UriHelper::buildHttpQuery('tiktok', ['token' => $token, '_d' => $user->getSlug()]);

                return UriHelper::redirectToUrl($to);
            } else {
                return UriHelper::redirectToUrl(
                    UriHelper::buildHttpQuery(
                        'errors',
                        ['m' => Lang::get('errors.login.tiktok.private_account')]
                    )
                );
            }
        } catch (\Exception $e) {
            Log::debug('Tiktok Login error', ['errorCode' => $errCode, 'errorMsg' => $e->getMessage()]);

            return UriHelper::redirectToUrl(
                UriHelper::buildHttpQuery(
                    'errors',
                    ['m' => Lang::get('errors.login.tiktok.generic')]
                )
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws JWTException
     */
    public function validateToken(Request $request)
    {
        if (!$request->bearerToken() || !Auth::check()) {
            throw new JWTException('Expired or Invalid token.');
        }

        return response()->json(
            [
                'validated' => true,
            ]
        );
    }
}
