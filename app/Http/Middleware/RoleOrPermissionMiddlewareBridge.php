<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleOrPermissionMiddlewareBridge
{
    private function targetClass(): string
    {
        foreach ([
            \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        ] as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        abort(500, 'Spatie RoleOrPermissionMiddleware class not found.');
    }

    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response
    {
        $middleware = app()->make($this->targetClass());

        return $middleware->handle($request, $next, ...$rolesOrPermissions);
    }

    public function terminate($request, $response): void
    {
        $middleware = app()->make($this->targetClass());

        if (method_exists($middleware, 'terminate')) {
            $middleware->terminate($request, $response);
        }
    }
}
