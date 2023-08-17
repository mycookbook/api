<?php

namespace App\Exceptions;

use Exception;

class TikTokException extends Exception
{
    public function __construct(string $message = "", array $context = [])
    {
        $message = $message . json_encode($context);
        parent::__construct($message, $code, $previous);
    }
}
