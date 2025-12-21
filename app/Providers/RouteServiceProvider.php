<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Clinica;
use App\Models\Paciente;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
   public const HOME = '/dashboard';   // o route('dashboard')


    /**
     * Define your route model bindings, pattern filters, and other route configuration.
	 
	 */
   public function boot(): void
    {
       

        // 2) Tu RateLimiter para API
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                        ->by($request->user()?->id ?: $request->ip());
        });

        // 3) Finalmente, registra tus rutas tal como ya lo tenías
        $this->routes(function () {
            Route::middleware('api')
                 ->prefix('api')
                 ->group(base_path('routes/api.php'));

            Route::middleware('web')
                 ->group(base_path('routes/web.php'));
        });

        Route::bind('paciente', function ($value) {
            if ($user = Auth::user()) {
                $clinica = Clinica::resolveForUser($user);
                $database = $clinica->db ?? $user->db;

                if ($database) {
                    config(['database.connections.tenant.database' => $database]);
                    DB::purge('tenant');
                    DB::reconnect('tenant');
                    DB::setDefaultConnection('tenant');
                }
            }

            return Paciente::on('tenant')->findOrFail($value);
        });
    }
}
