<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;
use App\Support\TenantDatabase;

class ConnectTenantDB
{
    public function handle($request, Closure $next)
    {

        //dd( Auth::user());
        if ($user = Auth::user()) {
            $clinica = Clinica::resolveForUser($user);
            $database = $clinica->db ?? $user->db;

            abort_unless($database, 403, 'No se pudo determinar la clínica del usuario.');

            TenantDatabase::connect($database);

        }

        return $next($request);
    }
}
