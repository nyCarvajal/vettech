<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\TravelCertificate;
use App\Policies\TravelCertificatePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TravelCertificate::class => TravelCertificatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            if (
                Schema::hasTable('roles') &&
                method_exists($user, 'hasRole') &&
                $user->hasRole('admin')
            ) {
                return true;
            }
            return null;
        });

        foreach ([
            'inventory.adjust', 'inventory.batch.manage', 'inventory.dispense',
            'sales.discount', 'sales.void', 'cash.open', 'cash.close', 'cash.expense',
            'hospital.admit', 'hospital.discharge', 'hospital.task.create',
        ] as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                if (! Schema::hasTable('permissions') || ! Schema::hasTable('permission_role') || ! Schema::hasTable('role_user')) {
                    return false;
                }

                $count = DB::table('permissions')
                    ->join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
                    ->join('role_user', 'permission_role.role_id', '=', 'role_user.role_id')
                    ->where('permissions.name', $permission)
                    ->where('role_user.user_id', $user->id)
                    ->count();
                return $count > 0;
            });
        }
    }
}
