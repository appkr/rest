<?php

namespace App\Exceptions;

use App\Exceptions\TokenNotProvidedException;
use App\Notifications\Slack\ErrorReporting;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        TokenNotProvidedException::class,
        TokenExpiredException::class,
        TokenInvalidException::class,
        TokenBlacklistedException::class,
        ModelNotFoundException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if (app()->environment('production')) {
            app(ErrorReporting::class)->send($e);
        }

        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (is_api_request()) {
            if ($e instanceof ModelNotFoundException or $e instanceof NotFoundHttpException) {
                return app('api.response')->notFoundError(
                    $e->getMessage() ?: trans('errors.resourceNotFound')
                );
            }

            if ($e instanceof MethodNotAllowedException or $e instanceof MethodNotAllowedHttpException) {
                return app('api.response')->setStatusCode(405)->error(
                    $e->getMessage() ?: trans('errors.notExistingEndpoint')
                );
            }

            if ($e instanceof JWTException) {
                return app('api.response')->setStatusCode($e->getStatusCode())->error($e->getMessage());
            }

            if ($e instanceof Exception) {
                return app('api.response')->error($e);
            }
        }

        if ($e instanceof ModelNotFoundException
            or $e instanceof NotFoundHttpException
            or $e instanceof MethodNotAllowedException
            or $e instanceof MethodNotAllowedHttpException) {
            return response(
                view('layouts.notice', [
                    'title'       => $e->getMessage() ?: trans('messages.whoops'),
                    'description' => trans('errors.resourceNotFound')
                ]),
                404
            );
        }

        return parent::render($request, $e);
    }
}
