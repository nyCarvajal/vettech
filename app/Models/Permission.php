<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Stancl\Tenancy\Tenancy;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = ['name', 'label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
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
