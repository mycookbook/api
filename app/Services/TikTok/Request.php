<?php

namespace App\Services\TikTok;

use GuzzleHttp\Client;

abstract class Request
{
    protected $httpClient;
    private $endpoint = '';

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
