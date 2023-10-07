<?php

declare(strict_types=1);

namespace App\Http\Clients;

use GuzzleHttp\Client;

class TikTokHttpClient
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('services.tiktok');
    }

    public function getAccessToken(string $code)
    {
        $response = $this->getClient()->request(
            'POST',
            $this->getUri() . '/oauth/access_token/',
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

    public function getUserInfo(string $open_id, string $access_token)
    {
        $userInfoResponse = $this->getClient()->request('POST',
            $this->getUri() . '/user/info/',
            [
                'json' => [
                    'open_id' => $open_id,
                    'access_token' => $access_token,
                    'fields' => [
                        'open_id',
                        'avatar_url',
                        'display_name',
                        'avatar_url_100',
                        'is_verified',
                        'profile_deep_link',
                        'bio_description',
                        'display_name',
                        'avatar_large_url',
                        'avatar_url_100',
                        'union_id',
                        'video_count'
                    ],
                ],
            ]
        );

        return json_decode($userInfoResponse->getBody()->getContents(), true);
    }

    private function getUri(): string
    {
        return $this->config['uri'] ?? '';
    }

    private function getClientId(): string
    {
        return $this->config['client_id'] ?? '';
    }

    private function getClientSecret(): string
    {
        return $this->config['client_secret'] ?? '';
    }

    private function getClient()
    {
        return new Client();
    }
}
