<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\Clinica;
use App\Support\TenantDatabase;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
	
	public function __construct()
    {
        // Este closure se ejecuta antes de cada método de cualquier controlador hijo
        $this->middleware(function ($request, $next) {
            if ($user = Auth::user()) {
                $peluqueria = Clinica::resolveForUser($user);
                $database = $peluqueria->db ?? $user->db;

                if (! $database) {
                    return $next($request);
                }

                // Inyecta la base tenant y la hace default
                TenantDatabase::connect($database);
            }

            return $next($request);
        });
    }
}
