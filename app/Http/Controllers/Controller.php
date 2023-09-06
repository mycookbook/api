<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Controller extends BaseController
{
    const RECIPE_RESOURCE = 'recipe';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function successResponse(array $data = [], $code = ResponseAlias::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $code);
    }

    public function noContentResponse(): Response
    {
        return response()->noContent();
    }

    public function errorResponse(array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, ResponseAlias::HTTP_BAD_REQUEST);
    }

    public function unauthorizedResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Your login session has expired. Please login.'
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
