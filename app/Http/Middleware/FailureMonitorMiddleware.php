<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FailureAnalysisService;
use Illuminate\Support\Facades\Log;

class FailureMonitorMiddleware
{
    /**
     * @var FailureAnalysisService
     */
    protected $analysisService;

    /**
     * @param FailureAnalysisService $analysisService
     */
    public function __construct(FailureAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $errorLogged = false;

        // Register a listener to detect if any error is logged during this request
        Log::listen(function ($message) use (&$errorLogged) {
            if (isset($message->level) && in_array($message->level, ['error', 'critical', 'alert', 'emergency'])) {
                $errorLogged = true;
            }
        });

        $response = $next($request);

        // Check for any error (Status code 400+ OR if an error was logged)
        if ($response->status() >= 400 || $errorLogged) {
            try {
                $analysis = $this->analysisService->analyze();

                // Correlate metrics with the failure
                Log::channel('daily')->error('API Error/Failure Detected', [
                    'endpoint' => $request->fullUrl(),
                    'method' => $request->method(),
                    'status' => $response->status(),
                    'error_logged_in_request' => $errorLogged,
                    'input' => $request->except(['password', 'password_confirmation']),
                    'analysis' => $analysis,
                ]);

                // Attach analysis to response headers if debugging is enabled
                if (config('app.debug')) {
                    $response->headers->set('X-Failure-Analysis', json_encode([
                        'status' => $analysis['status'],
                        'suggested_cause' => $analysis['suggested_cause']
                    ]));
                }
            } catch (\Exception $e) {
                Log::error('Failure analysis failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
