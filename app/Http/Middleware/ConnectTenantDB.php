<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectTenantDB
{
    public function handle($request, Closure $next)
    {

        //dd( Auth::user());
        if ($user = Auth::user()) {
            $clinica = Clinica::resolveForUser($user);
            $database = $clinica->db ?? $user->db;

            abort_unless($database, 403, 'No se pudo determinar la clínica del usuario.');

            // 1) Inyecta en la conexión tenant
            config(['database.connections.tenant.database' => $database]);
            config(['database.default' => 'tenant']); // si quieres que 'tenant' sea default

            // 2) Purga y reconecta sobre la DB correcta
            DB::purge('tenant');

            $connection = DB::connection('tenant');
            $connection->setDatabaseName($database);
            $connection->reconnect();

            // Asegura que la sesión SQL quedó apuntando a la DB correcta.
            // Algunos hosts no respetan el nombre configurado hasta ejecutar "USE <db>".
            $connection->getPdo()->exec("use `{$database}`");

            // Valida que el motor realmente seleccionó la base de datos.
            $selected = optional($connection->selectOne('select database() as db'))->db;
            if ($selected !== $database) {
                abort(500, 'No se pudo seleccionar la base de datos del tenant.');
            }

            // 3) Forzar "USE <db>" y dejar el default en tenant
            DB::setDefaultConnection('tenant');

            // 4) Valida que la DB quedó seleccionada; si no, aborta temprano
            if ($connection->getDatabaseName() !== $database) {
                abort(500, 'No se pudo seleccionar la base de datos del tenant.');
            }

        }

        return $next($request);
    }
}
