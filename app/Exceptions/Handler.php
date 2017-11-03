<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e exception
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request request
     * @param \Exception               $e       exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof UnauthorizedHttpException) {
            switch (get_class($e->getPrevious())) {
            case TokenExpiredException::class:
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Token has expired'
                    ], $e->getStatusCode()
                );
            case TokenInvalidException::class:

            case TokenBlacklistedException::class:
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Token is invalid'
                    ], $e->getStatusCode()
                );
            default:
                break;
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
                    'docs' => $docs['api']
                ], $e->getStatusCode()
            );
        }

        return  parent::render($request, $e);
    }
}
