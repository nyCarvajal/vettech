<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Stancl\Tenancy\Tenancy;

class Role extends SpatieRole
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = ['name', 'label'];

   public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            config('permission.table_names.role_has_permissions'),
            config('permission.column_names.role_pivot_key') ?? 'role_id',
            config('permission.column_names.permission_pivot_key') ?? 'permission_id'
        );
    }

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

        return $this->connection ?? parent::getConnectionName();
    }
}
