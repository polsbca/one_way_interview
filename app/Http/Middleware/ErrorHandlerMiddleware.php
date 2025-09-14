<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErrorHandlerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Log request information for debugging
            if (config('app.debug')) {
                Log::info('Request received', [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'user_id' => auth()->id(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            $response = $next($request);

            // Log response information for debugging
            if (config('app.debug') && $response instanceof \Illuminate\Http\Response) {
                Log::info('Response sent', [
                    'status' => $response->getStatusCode(),
                    'url' => $request->url(),
                    'method' => $request->method(),
                ]);
            }

            return $response;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Model not found', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'status' => 404,
                ], 404);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'The requested resource was not found.');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Authorization failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This action is unauthorized.',
                    'status' => 403,
                ], 403);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'You are not authorized to perform this action.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('Validation failed', [
                'errors' => $e->errors(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                    'status' => 422,
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please fix the errors below.');

        } catch (\Illuminate\Auth\AuthenticationException $e) {
            Log::info('Authentication failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'status' => 401,
                ], 401);
            }

            return redirect()->route('login');

        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            Log::warning('Route not found', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found.',
                    'status' => 404,
                ], 404);
            }

            return response()->view('errors.404', [], 404);

        } catch (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            Log::warning('Method not allowed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed.',
                    'status' => 405,
                ], 405);
            }

            return response()->view('errors.405', [], 405);

        } catch (\Exception $e) {
            Log::error('Unexpected error occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again.',
                    'status' => 500,
                ], 500);
            }

            return redirect()->route('admin.dashboard')
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
