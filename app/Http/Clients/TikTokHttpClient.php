<?php

declare(strict_types=1);

namespace App\Http\Clients;

use App\Dtos\TikTokUserDto;
use App\Http\Clients\Enums\UserInfoEnum;
use App\Http\Clients\Enums\VideoListEnum;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

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

    public function getUserInfo(string $access_token)
    {
        $fields =  implode( ',', UserInfoEnum::values());

        $userInfoResponse = $this->client->request('GET',
            $this->getV2DisplayApiEndpoint() . '/user/info/?fields=' . $fields,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json'
                ]
            ]
        );

        return json_decode($userInfoResponse->getBody()->getContents(), true);
    }

    public function listVideos(TikTokUserDto $userDto): array
    {
        $fields = implode( ',', VideoListEnum::values());

        $response = $this->client->request('POST',
            $this->getV2DisplayApiEndpoint() . '/video/list/?fields=' . $fields,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $userDto->getCode(),
                    'Content-Type' => 'application/json'
                ]
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
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
