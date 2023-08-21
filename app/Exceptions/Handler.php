<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $throwable
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $throwable)
    {
        if ($throwable instanceof ValidationException) {
            return response()->json($throwable->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($throwable instanceof UnauthorizedException) {
            return response()->json([
                'error' => $throwable->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($throwable instanceof JWTException || $throwable instanceof TokenInvalidException) {
            return response()->json([
                'error' => $throwable->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($throwable instanceof InvalidPayloadException) {
            return response()->json(array_merge([
                'error' => $throwable->getMessage()
            ], $throwable->getContext()), $throwable->getCode());
        }

        if ($throwable instanceof TikTokException) {
            return response()->json(['error' => $throwable->getMessage()]);
        }

        if ($throwable instanceof ApiException) {
            return response()->json(['error' => $throwable->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return parent::render($request, $throwable);
    }
}
