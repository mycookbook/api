<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedClientException extends \Exception
{
    public function report(): void
    {
    }

    public function render()
    {
        return response()->json([
            'error' => 'Unauthorized',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
