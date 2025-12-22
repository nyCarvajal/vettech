<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! empty($user->role)) {
            $role = strtolower($user->role);
        } elseif (method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty()) {
            $role = strtolower($user->getRoleNames()->first());
        } else {
            $role = null;
        }

        return match ($role) {
            'admin', 'administrator' => redirect()->route('dashboard.admin'),
            'medico', 'médico' => redirect()->route('dashboard.medico'),
            'contador' => redirect()->route('dashboard.contador'),
            default => abort(403, 'Rol no permitido para dashboard'),
        };
    }
}
