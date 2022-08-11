<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class CookbookModelNotFoundException extends \Exception
{
    public function report(): void
    {
    }

    public function render()
    {
        return response()->json([
            'error' => 'Record Not found.',
        ], Response::HTTP_NOT_FOUND);
    }
}
