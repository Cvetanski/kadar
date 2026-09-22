<?php

use App\Http\Middleware\EnsureCreatorOnboarded;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\RequiresSubscription;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'onboarded' => EnsureCreatorOnboarded::class,
            'admin' => EnsureIsAdmin::class,
            'subscribed' => RequiresSubscription::class,
        ]);

        // Paddle posts webhook events without a Laravel session/CSRF token.
        $middleware->validateCsrfTokens(except: [
            'webhooks/paddle',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
