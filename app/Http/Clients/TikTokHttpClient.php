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
            $this->getV1DisplayApiHostname() . '/oauth/access_token/',
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
            ['access_token' => $access_token, 'uri' => '/user/info/']
        );
    }

    public function listVideos(TikTokUserDto $userDto): array
    {
        return $this->makeHttpRequest(
            AllowedHttpMethod::POST,
            VideoListEnum::values(),
            ['access_token' => $userDto->getCode(), 'uri' => '/video/list/']
        );
    }

    private function makeHttpRequest(AllowedHttpMethod $httpMethod, $fields = [], $options = []): array
    {
        $headers = ['headers' => ['Content-Type' => 'application/json']];
        $hostname = $this->getV2DisplayApiHostname();

        if ($bearer = Arr::get($options, 'access_token')) {
            $headers['headers']['Authorization'] = 'Bearer ' . $bearer;
        }

        if ($uri = Arr::get($options, 'uri')) {
            $hostname = $hostname . $uri . '?fields=' . implode( ',', $fields);
        }

        try {
            $response = $this->client->request($httpMethod->value, $hostname, $headers);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $exception) {
            Log::debug(
                'Tiktok: error retrieving user info or listing videos',
                [
                    'method' => $httpMethod,
                    'uri' => $uri,
                    'exception' => $exception,
                    'options' => $options,
                    'fields' => $fields
                ]
            );

            return [];
        }
    }

    private function getV1DisplayApiHostname(): string
    {
        return Arr::get($this->config, 'v1_host');
    }

    private function getClientId(): string
    {
        return Arr::get($this->config, 'client_id');
    }

    private function getClientSecret(): string
    {
        return Arr::get($this->config, 'client_secret');
    }

    private function getV2DisplayApiHostname(): string
    {
        return Arr::get($this->config, 'v2_host');
    }
}
