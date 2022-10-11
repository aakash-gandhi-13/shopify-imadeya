<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use ParseError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     * @param  Illuminate\Http\Request  $request
     * @param  Throwable  $exception
     * @return Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'result' => false,
                'message' => trans('messages.validation_error'),
                'errors' => $exception->errors()
            ], 422);
        }
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'result' => false,
                'message' => trans('messages.unauthorized'),
                'errors' => $exception->getMessage()
            ], 401);
        }
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'result' => false,
                'message' => trans('messages.not_found'),
                'errors' => $exception->getMessage()
            ], 404);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'result' => false,
                'message' => trans('messages.invalid_route'),
                'errors' => $exception->getMessage()
            ], 404);
        }
        $msg = ($exception instanceof ParseError) ? 'Internal server error' : trans('messages.internal_server_error');
        $data = [
            'result' => false,
            'message' => $msg,
            'errors' => $exception->getMessage()
        ];
        if (app()->isLocal() && (getenv('APP_DEBUG') == 'true')) {
            $data['trace'] = $exception->getTrace();
        }
        return response()->json($data, 500);
    }
}
