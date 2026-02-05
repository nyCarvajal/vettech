<?php

use App\Http\Middleware\ConnectTenantDB;
use App\Http\Middleware\EnsureClinicFeatureEnabled;
use App\Http\Middleware\EnsureRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware as MiddlewareConfigurator;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (MiddlewareConfigurator $middleware) {
        // Esto añade tu middleware al final del stack global
        $middleware->append(ConnectTenantDB::class);

        // Garantiza que la sesión y la autenticación sucedan antes de conectar la BD
        // del tenant, y que la conexión esté lista antes del route model binding.
        $middleware->priority([
            StartSession::class,
            Authenticate::class,
            ConnectTenantDB::class,
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'ensureRole' => EnsureRole::class,
            'feature' => EnsureClinicFeatureEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $exception) {
            $message = sprintf('[%s] %s in %s:%d', class_basename($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());

            File::ensureDirectoryExists(storage_path('logs'));
            if (! File::exists(storage_path('logs/laravel.log'))) {
                File::put(storage_path('logs/laravel.log'), '');
            }

            Log::channel('single')->error($message, [
                'exception' => $exception,
                'url' => request()?->fullUrl(),
                'method' => request()?->method(),
                'ip' => request()?->ip(),
            ]);
        });
    })->create();
