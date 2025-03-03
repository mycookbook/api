<?php

namespace App\Services\TikTok;

use GuzzleHttp\Client;

abstract class Request
{
    protected Client $httpClient;
    private string $endpoint = '';

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public abstract function handle(): void;

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
