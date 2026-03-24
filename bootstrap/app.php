<?php

use App\Http\Middleware\ConnectTenantDB;
use App\Http\Middleware\EnsureClinicFeatureEnabled;
use App\Http\Middleware\EnsureRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Se mantiene el nombre original por simplicidad
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Asegúrate de tener esta línea si usas routes/api.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Excepción de CSRF para el Webhook de WhatsApp
        $middleware->validateCsrfTokens(except: [
            'api/webhook/whatsapp',
        ]);

        // 2. Middleware Global
        $middleware->append(ConnectTenantDB::class);

        // 3. Prioridad de Middleware
        $middleware->priority([
            StartSession::class,
            Authenticate::class,
            ConnectTenantDB::class,
            SubstituteBindings::class,
        ]);

        // 4. Alias de Middleware
        $middleware->alias([
            'ensureRole' => EnsureRole::class,
            'feature' => EnsureClinicFeatureEnabled::class,
            'role' => \App\Http\Middleware\RoleMiddlewareBridge::class,
            'permission' => \App\Http\Middleware\PermissionMiddlewareBridge::class,
            'role_or_permission' => \App\Http\Middleware\RoleOrPermissionMiddlewareBridge::class,
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