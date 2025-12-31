<?php

namespace App\Http\Controllers;

use App\Models\Cage;
use App\Models\Clinica;
use App\Support\TenantDatabase;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\ConnectTenantDB;
use Illuminate\View\View;

class HospitalBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware(ConnectTenantDB::class);
        $this->middleware(function ($request, $next) {
            $this->ensureTenantConnection();

            return $next($request);
        });
    }

    public function __invoke(): View
    {
        $cages = Cage::with(['stays' => function ($query) {
            $query->where('status', 'active')->latest();
        }, 'stays.tasks.logs', 'stays.patient.species', 'stays.patient.breed', 'stays.owner'])->get();

        return view('hospital.board', compact('cages'));
    }

    private function ensureTenantConnection(): void
    {
        if (config('database.connections.tenant.database')) {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $clinica = Clinica::resolveForUser($user);
        $database = $clinica->db ?? $user->db;

        abort_unless($database, 403, 'No se pudo determinar la clínica del usuario.');

        TenantDatabase::connect($database);
    }
}
