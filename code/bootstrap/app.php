<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'locale' => \App\Http\Middleware\LocaleMiddleware::class,
            'current_year_data' => \App\Http\Middleware\CurrentAcademicYearData::class,
            'student_setup' => \App\Http\Middleware\StudentProfileSetup::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Global middleware
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class, // Check maintenance mode first
            \App\Http\Middleware\LocaleMiddleware::class,
            \App\Http\Middleware\StudentProfileSetup::class,
        ]);

        // API middleware configuration - Sanctum for API authentication
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
