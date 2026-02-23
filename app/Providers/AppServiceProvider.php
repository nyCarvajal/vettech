<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Reserva;
use App\Models\Paciente;
use App\Models\Clinica;
use App\Support\TenantDatabase;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Event::listen(RouteMatched::class, function (RouteMatched $event) {
            if ($user = Auth::user()) {
                $peluqueria = Clinica::resolveForUser($user);

                $database = $peluqueria->db ?? $user->db;

                if (! $database) {
                    return;
                }

                TenantDatabase::connect($database);
            }
        });

        View::composer(['layouts.app', 'layouts.vertical', 'layouts.partials.topbar'], function ($view) {
            static $count = null;
            static $birthdayCount = null;

            if ($count === null) {
                $count = 0;

                if ($user = Auth::user()) {
                    $peluqueria = Clinica::resolveForUser($user);

                    $database = $peluqueria->db ?? $user->db;

                    if ($database) {
                        TenantDatabase::connect($database);

                        if (Schema::connection('tenant')->hasTable('reservas')) {
                            $count = Reserva::where('estado', 'Pendiente')->count();
                        }
                    }
                }
            }

            if ($birthdayCount === null) {
                $birthdayCount = 0;

                if ($user = Auth::user()) {
                    $peluqueria = Clinica::resolveForUser($user);

                    $database = $peluqueria->db ?? $user->db;

                    if ($database) {
                        TenantDatabase::connect($database);

                        $today = Carbon::today();

                        if (Schema::connection('tenant')->hasTable('pacientes')) {
                            $birthdayCount = Paciente::whereNotNull('fecha_nacimiento')
                                ->whereMonth('fecha_nacimiento', $today->month)
                                ->whereDay('fecha_nacimiento', $today->day)
                                ->count();
                        }
                    }
                }
            }

            $view->with('pendingReservationsCount', $count);
            $view->with('todayBirthdayCount', $birthdayCount);
        });
    }
}

