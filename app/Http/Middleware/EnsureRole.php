<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user) {
            throw new AccessDeniedHttpException('No autenticado.');
        }

        if (! empty($user->role) && in_array(strtolower($user->role), array_map('strtolower', $roles), true)) {
            return $next($request);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole($roles)) {
            return $next($request);
        }

        throw new AccessDeniedHttpException('No autorizado para esta sección.');
    }
}
