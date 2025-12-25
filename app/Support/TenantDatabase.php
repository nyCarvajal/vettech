<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantDatabase
{
    /**
     * Configure and select the tenant database for the current request.
     */
    public static function connect(string $database): void
    {
        Config::set('database.connections.tenant.database', $database);

        DB::purge('tenant');

        $connection = DB::connection('tenant');
        $connection->setDatabaseName($database);
        $connection->reconnect();

        // Force the DB session to target the tenant schema and validate the selection.
        $connection->getPdo()->exec("use `{$database}`");
        $selected = optional($connection->selectOne('select database() as db'))->db;

        abort_if($selected !== $database, 500, 'No se pudo seleccionar la base de datos del tenant.');

        DB::setDefaultConnection('tenant');
    }
}
