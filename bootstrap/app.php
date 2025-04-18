<?php

use App\Http\Middleware\RequestExecutionTimeLogger;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{
    Exceptions,
    Middleware
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       // Register the custom middleware globally
       $middleware->use([
            RequestExecutionTimeLogger::class
        ]);

        $middleware->alias([
            'request.time.log' => RequestExecutionTimeLogger::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
