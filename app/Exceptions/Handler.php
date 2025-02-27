<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Log::error('exception handler ' . $exception->getMessage());
        // Log::error('exception handler ' . $exception);
        //Log::info('Request Headers:', $request->headers->all());

        // Handle 401 Unauthorized (Authentication Failed)
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            //dd($request->expectsJson());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return inertia('Errors/401'); // Reference your React 401 component
        }

        // Check if the exception is a 403 Forbidden error
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 403) {
            // For API requests, return JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            // For regular requests, return an Inertia page
            return inertia('Errors/403'); // Reference your React component here
        }

        // Handle 404 Not Found
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            return inertia('Errors/404'); // Reference your React 404 component
        }

        // Handle 500 Internal Server Error
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 500) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Server Error'], 500);
            }
            return inertia('Errors/500'); // Reference your React 500 component
        }

        // Handle 419 Page Expired (typically for CSRF token mismatch)|| $exception instanceof \Illuminate\Session\TokenMismatchException
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException  && $exception->getStatusCode() === 419 || $exception instanceof \Illuminate\Session\TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page Expired'], 419);
            }
            return inertia('Errors/419'); // Reference your React 419 component
        }

        // Default rendering for other exceptions
        return parent::render($request, $exception);
    }
}
