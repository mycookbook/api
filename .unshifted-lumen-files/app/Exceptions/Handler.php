<?php

namespace App\Exceptions;

use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Class Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
//    protected $dontReport = [
//        AuthorizationException::class,
//        HttpException::class,
//        ModelNotFoundException::class,
//        ValidationException::class,
//    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @return void
     *
     * @throws Exception
     */
    public function report(\Throwable $e)
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, \Throwable $e)
    {
        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        if ($e instanceof UnauthorizedHttpException) {
            if (is_null($e->getPrevious())) {
                return response()->json(
                    [
                        'status' => 'Unauthorized',
                        'message' => 'Token is required',
                    ], $e->getStatusCode()
                );
            }

            switch (get_class($e->getPrevious())) {
                case TokenInvalidException::class:

                case TokenBlacklistedException::class:
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Token is invalid',
                        ], $e->getStatusCode()
                    );
                case TokenExpiredException::class:
                    return response()->json(
                        [
                            'status' => 'error',
                            'message' => 'Token has expired',
                        ], $e->getStatusCode()
                    );
            }
        }

        if ($e instanceof MethodNotAllowedHttpException
            || $e instanceof NotFoundHttpException
        ) {
            $docs = include __DIR__.'/../../config/docs.php';

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Method Not Allowed or Not Found. Check API Docs',
                    'docs' => $docs['api'],
                ], $e->getStatusCode()
            );
        }

        if ($e instanceof UnprocessibleEntityException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }

        return parent::render($request, $e);
    }
}