<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        \Illuminate\Auth\AuthenticationException::class => 'warning',
        \Illuminate\Auth\Access\AuthorizationException::class => 'warning',
        \Illuminate\Validation\ValidationException::class => 'info',
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'info',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\AuthenticationException::class,
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
            // Log all exceptions with context
            if (app()->environment('production')) {
                \Log::error('Exception occurred', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'user_id' => auth()->id(),
                    'user_agent' => request()->userAgent(),
                    'ip' => request()->ip(),
                ]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle AJAX/JSON requests differently
        if ($request->expectsJson()) {
            return $this->handleJsonException($exception);
        }

        // Handle specific exceptions
        if ($exception instanceof ModelNotFoundException) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'The requested resource was not found.');
        }

        if ($exception instanceof AuthorizationException) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You are not authorized to perform this action.');
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->view('errors.405', [], 405);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle JSON exceptions for API responses
     *
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleJsonException(Throwable $exception)
    {
        $status = 500;
        $message = 'An error occurred while processing your request.';
        $errors = null;

        if ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'The given data was invalid.';
            $errors = $exception->errors();
        } elseif ($exception instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated.';
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = 'This action is unauthorized.';
        } elseif ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Resource not found.';
        } elseif ($exception instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Route not found.';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'Method not allowed.';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status' => $status,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        // Add debug information in development
        if (app()->environment('local') || app()->environment('development')) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        return response()->json($response, $status);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'status' => 401,
            ], 401);
        }

        return redirect()->route('login');
    }
}
