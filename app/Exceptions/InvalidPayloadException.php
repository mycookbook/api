<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidPayloadException extends Exception
{
    protected $context;

    public function __construct(string $message, array $context)
    {
        $this->context = $context;
        parent::__construct($message, 400);
    }

    public function getContext()
    {
        return $this->context;
    }
}
