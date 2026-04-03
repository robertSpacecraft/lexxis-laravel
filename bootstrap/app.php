<?php

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\TrustProxies::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isApiRequest = function (Request $request): bool {
            return $request->is('api/*') || $request->expectsJson();
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApiRequest) {
            if (!$isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                message: 'Validation error',
                errors: $e->errors(),
                status: 422
            );
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApiRequest) {
            if (!$isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                message: 'Unauthenticated',
                status: 401
            );
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) use ($isApiRequest) {
            if (!$isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                message: 'Resource not found',
                status: 404
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) use ($isApiRequest) {
            if (!$isApiRequest($request)) {
                return null;
            }

            if ($e instanceof HttpResponseException) {
                return null;
            }

            $status = $e->getStatusCode();

            return ApiResponse::error(
                message: SymfonyResponse::$statusTexts[$status] ?? 'HTTP error',
                status: $status
            );
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($isApiRequest) {
            if (!$isApiRequest($request)) {
                return null;
            }

            return ApiResponse::error(
                message: app()->hasDebugModeEnabled()
                    ? $e->getMessage()
                    : 'Server error',
                status: 500
            );
        });
    })
    ->create();
