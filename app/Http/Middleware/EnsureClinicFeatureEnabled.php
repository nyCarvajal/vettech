<?php

namespace App\Http\Middleware;

use App\Support\ClinicaActual;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicFeatureEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $clinica = ClinicaActual::get();

        if (! $clinica->featureEnabled($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu plan no incluye este módulo.',
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->view('errors.feature-unavailable', [
                'feature' => $feature,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
