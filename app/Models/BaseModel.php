<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Tenancy;

class BaseModel extends Model
{
    /**
     * Always point tenant-bound models to the tenant connection.
     * The actual database name is injected at runtime by middleware,
     * but forcing the connection prevents accidental reads from the
     * shared "comun" database (e.g., /owners/1).
     */
    protected $connection = 'tenant';

    /**
     * Devuelve 'tenant' solo cuando la tenencia está inicializada.
     * Si aún no se ha resuelto el tenant, usa la conexión por defecto.
     */
    public function getConnectionName()
    {
        /** @var Tenancy|null $tenancy */
        $tenancy = app()->bound('tenancy') ? app('tenancy') : null;

        if ($tenancy && $tenancy->initialized) {
            return 'tenant';
        }

        // Si ya existe una base de datos configurada para la conexión tenant,
        // úsala para evitar leer desde la base "comun" en rutas que sí
        // inicializan la conexión pero aún no el objeto Tenancy.
        $tenantDatabase = config('database.connections.tenant.database');

        return $tenantDatabase
            ? 'tenant'
            : parent::getConnectionName();
    }
}
