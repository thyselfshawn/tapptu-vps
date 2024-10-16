<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckOwner;
use App\Http\Middleware\PremiumMember;
use App\Http\Middleware\StandardMember;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // append middleware
        $middleware->appendToGroup('admin', [
            CheckAdmin::class,
        ]);
        $middleware->appendToGroup('venue', [
            CheckOwner::class,
        ]);
        $middleware->appendToGroup('premium', [
            PremiumMember::class,
        ]);
        $middleware->appendToGroup('standard', [
            StandardMember::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
