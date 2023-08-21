<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const RECIPE_RESOURCE = 'recipe';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse(array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data);
    }

    public function noContentResponse(): Response
    {
        return response()->noContent();
    }

    public function errorResponse(array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, Response::HTTP_BAD_REQUEST);
    }

    public function unauthorizedResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Your login session has expired. Please login.'
        ], Response::HTTP_UNAUTHORIZED);
    }
}
