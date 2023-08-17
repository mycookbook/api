<?php

namespace App\Services\TikTok;

use Illuminate\Support\Facades\Config;

class HttpRequestRunner
{
    public function __invoke(array $config, bool $async = false, Request...$requests)
    {
        $this->validateConfig($config);

        if ($async === false) {
           foreach ($requests as $synchronousRequest) {
               $synchronousRequest->handle();
           }
       } else {
           $this->handleSync();
       }

        return $this;
    }

    //todo
    public function handleSync() {}

    public function getContents()
    {
        return Config::get('tiktok');
    }

    private function setCode(string $code)
    {
        Config::set('tiktok', ['code' => $code]);
    }

    private function validateConfig(array $options = [])
    {
        foreach ($options as $i => $j) {
            if (is_numeric($i)) {
                throw new \Exception('Invalid type. Must be a key/value pair.');
            }
        }

        $this->setCode($options['code']);
    }
}
