<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get timeout value from constants
        $timeout = config('constants.api_timeout');

        // Set the execution time limit for this request
        if ($timeout > 0) {
            set_time_limit($timeout);
        }

        try {
            return $next($request);
        } catch (Throwable $e) {
            // Handle timeout errors for API routes
            if ($request->is('api/*') && str_contains($e->getMessage(), 'Maximum execution time')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request timeout: Maximum execution time exceeded',
                    'data' => [
                        'configured_timeout' => $timeout,
                        'status' => 'timeout',
                        'error' => 'The request exceeded the maximum execution time limit.',
                    ],
                ], 408);
            }

            throw $e;
        }
    }
}
