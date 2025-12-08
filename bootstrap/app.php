<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //ajouter le middleware admin
        $middleware->alias([
            'admin' => App\Http\Middleware\Admin::class,
            'client' => App\Http\Middleware\ClientMiddleware::class,
        ]);
        
        // Exclure le webhook Wave de la vérification CSRF
        $middleware->validateCsrfTokens(except: [
            'webhook/wave/payment',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();



