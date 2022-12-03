<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

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

        if ($throwable instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
            return response()->json([
                'error' => $throwable->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        return parent::render($request, $throwable);
    }
}
