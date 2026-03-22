<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'coach' => \App\Http\Middleware\CoachMiddleware::class,
            'admin_or_coach' => \App\Http\Middleware\AdminOrCoachMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (HttpException $e, $request) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'An error occurred.'
                ], $e->getStatusCode());
            }

            $status = $e->getStatusCode();
            $message = match($status) {
                403 => 'You are not authorized to perform this action.',
                404 => 'The requested resource was not found.',
                default => 'An error occurred.',
            };

            return redirect()->back()->with('error', $message);
        });
    })->create();