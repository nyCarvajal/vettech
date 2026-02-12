<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddlewareBridge
{
    private function targetClass(): string
    {
        foreach ([
            \Spatie\Permission\Middleware\RoleMiddleware::class,
            \Spatie\Permission\Middlewares\RoleMiddleware::class,
        ] as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        abort(500, 'Spatie RoleMiddleware class not found.');
    }

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $middleware = app()->make($this->targetClass());

        return $middleware->handle($request, $next, ...$roles);
    }

    public function terminate($request, $response): void
    {
        $middleware = app()->make($this->targetClass());

        if (method_exists($middleware, 'terminate')) {
            $middleware->terminate($request, $response);
        }
    }
}
