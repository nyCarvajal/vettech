<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Tenancy;

class BaseModel extends Model
{
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
