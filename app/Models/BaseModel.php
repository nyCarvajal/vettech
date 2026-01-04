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

        if ($tenancy && $tenancy->initialized && $tenancy->tenant) {
            return 'tenant';
        }

        $tenantDatabase = config('database.connections.tenant.database');

        if ($tenantDatabase) {
            return 'tenant';
        }

        // Default to the model's declared connection ("tenant") so consent
        // lookups never fall back to the shared "mysql" database, even when
        // Tenancy has not been fully bootstrapped yet.
        return $this->connection ?? parent::getConnectionName();
    }
}
