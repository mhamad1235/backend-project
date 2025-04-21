<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;

use App\Http\Middleware\AlwaysAcceptJson;
use App\Http\Middleware\ApiLocalization;
use App\Http\Middleware\EnsureSystemKey;
use App\Http\Middleware\Localization;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SetViewTitle;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            Localization::class,
            SetViewTitle::class,
        ]);

        $middleware->api([
            AlwaysAcceptJson::class,
            ApiLocalization::class,
            EnsureSystemKey::class,
            SanitizeInput::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'result' => false,
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => "Unauthenticated.",
                ], 401);
            }
        });
    })->create();
