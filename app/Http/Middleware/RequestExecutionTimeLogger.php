<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestExecutionTimeLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start measuring time
        $startTime = microtime(true);

        // Proceed with the request
        $response = $next($request);

        // Calculate execution time
        $executionTime = microtime(true) - $startTime;

        // Log the request details
        $this->logRequestDetails($request, $response, $executionTime);

        return $response;
    }

    /**
     * Log detailed request information
     */
    protected function logRequestDetails(Request $request, Response $response, float $executionTime): void
    {
        // Prepare log data
        $logData = [
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'execution_time' => round($executionTime, 4) . ' seconds',
            'status_code' => $response->getStatusCode(),
        ];

        // Add user information if authenticated
        if (auth()->check()) {
            $logData['user_id'] = auth()->id();
            $logData['user_email'] = auth()->user()->email;
        }

        // Add query parameters if present
        if ($request->query()) {
            $logData['query_params'] = $request->query();
        }

        // Log the information
        Log::channel('request_time')->info('Request Execution Time', $logData);
    }
}
