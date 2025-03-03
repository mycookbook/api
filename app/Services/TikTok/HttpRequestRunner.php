<?php

namespace App\Services\TikTok;

use Exception;
use Illuminate\Support\Facades\Config;

class HttpRequestRunner
{
    /**
     * @param array<string> $config
     * @param bool $async
     * @param Request ...$requests
     * @return $this
     * @throws Exception
     */
    public function __invoke(array $config, bool $async = false, Request...$requests): self
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
    public function handleSync(): void {}

    public function getContents(): array
    {
        return Config::get('tiktok');
    }

    private function setCode(string $code): void
    {
        Config::set('tiktok', ['code' => $code]);
    }

    /**
     * @param array<string> $options
     * @return void
     * @throws Exception
     */
    private function validateConfig(array $options = []): void
    {
        foreach ($options as $i => $j) {
            if (is_numeric($i)) {
                throw new Exception('Invalid type. Must be a key/value pair.');
            }
        }

        $this->setCode($options['code']);
    }
}
