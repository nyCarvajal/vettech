<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddlewareBridge
{
    private function targetClass(): string
    {
        foreach ([
            \Spatie\Permission\Middleware\PermissionMiddleware::class,
            \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        ] as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        abort(500, 'Spatie PermissionMiddleware class not found.');
    }

    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $middleware = app()->make($this->targetClass());

        return $middleware->handle($request, $next, ...$guards);
    }

    public function terminate($request, $response): void
    {
        $middleware = app()->make($this->targetClass());

        if (method_exists($middleware, 'terminate')) {
            $middleware->terminate($request, $response);
        }
    }
}
