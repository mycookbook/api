<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Dtos\TikTokUserDto;
use App\Http\Clients\Enums\AllowedHttpMethod;
use App\Http\Clients\Enums\UserInfoEnum;
use App\Http\Clients\Enums\VideoListEnum;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TikTokHttpClient
{
    protected array $config;
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->config = config('services.tiktok');
        $this->client = $client;
    }

    public function getAccessToken(string $code)
    {
        $response = $this->client->request(
            'POST',
            $this->getV1BaseUri() . '/oauth/access_token/',
            [
                'form_params' => [
                    'client_key' => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ]
        );

        $decoded = json_decode($response->getBody()->getContents(), true);

        if ($decoded['message'] === 'error') {
            throw new \Exception(json_encode($decoded));
        }

        return $decoded;
    }

    public function getUserInfo(string $access_token): array
    {
        return $this->makeHttpRequest(
            AllowedHttpMethod::GET,
            UserInfoEnum::values(),
            ['Authorization' => $access_token, 'Path' => '/user/info/?fields=']
        );
    }

    public function listVideos(TikTokUserDto $userDto): array
    {
        return $this->makeHttpRequest(
            AllowedHttpMethod::POST,
            VideoListEnum::values(),
            ['Authorization' => $userDto->getCode(), 'Path' => '/video/list/?fields=']
        );
    }

    private function makeHttpRequest(AllowedHttpMethod $httpMethod, $fields = [], $headers = []): array
    {
        $options = ['headers' => ['Content-Type' => 'application/json']];
        $v2DisplayApiEndpoint = $this->getV2DisplayApiEndpoint();

        if ($bearer = Arr::get($headers, 'Authorization')) {
            $options['headers']['Authorization'] = 'Bearer ' . $bearer;
        }

        if ($path = Arr::get($headers, 'Path')) {
            $v2DisplayApiEndpoint = $v2DisplayApiEndpoint . $path . implode( ',', $fields);
        }

        try {
            $response = $this->client->request($httpMethod->value, $v2DisplayApiEndpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $exception) {
            Log::debug(
                'Tiktok: error retrieving user info or listing videos',
                [
                    'resource' => $path,
                    'errorMsg' => $exception->getMessage()
                ]
            );

            return [];
        }
    }

    private function getV1BaseUri(): string
    {
        return Arr::get($this->config, 'uri');
    }

    private function getClientId(): string
    {
        return Arr::get($this->config, 'client_id');
    }

    private function getClientSecret(): string
    {
        return Arr::get($this->config, 'client_secret');
    }

    private function getV2DisplayApiEndpoint(): string
    {
        return 'https://open.tiktokapis.com/v2';
    }
}
