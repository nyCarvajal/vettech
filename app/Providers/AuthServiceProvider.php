<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\TravelCertificate;
use App\Policies\TravelCertificatePolicy;
use App\Models\ConsentTemplate;
use App\Models\ConsentDocument;
use App\Policies\ConsentTemplatePolicy;
use App\Policies\ConsentDocumentPolicy;
use App\Models\Followup;
use App\Policies\FollowupPolicy;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TravelCertificate::class => TravelCertificatePolicy::class,
        ConsentTemplate::class => ConsentTemplatePolicy::class,
        ConsentDocument::class => ConsentDocumentPolicy::class,
        Followup::class => FollowupPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            $connection = $this->permissionConnection();

            if (! $connection) {
                return null;
            }

            if (! Schema::connection($connection)->hasTable('roles') || ! Schema::connection($connection)->hasTable('model_has_roles')) {
                return null;
            }

            $hasAdminRole = DB::connection($connection)
                ->table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('model_type', get_class($user))
                ->where('model_id', $user->getAuthIdentifier())
                ->where('roles.name', 'admin')
                ->exists();

            return $hasAdminRole ? true : null;
        });

        foreach ([
            'inventory.adjust', 'inventory.batch.manage', 'inventory.dispense',
            'sales.discount', 'sales.void', 'cash.open', 'cash.close', 'cash.expense',
            'hospital.admit', 'hospital.discharge', 'hospital.task.create',
        ] as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                $connection = $this->permissionConnection();

                if (! $connection) {
                    return false;
                }

                if (
                    ! Schema::connection($connection)->hasTable('permissions') ||
                    ! Schema::connection($connection)->hasTable('permission_role') ||
                    ! Schema::connection($connection)->hasTable('role_user')
                ) {
                    return false;
                }

                $count = DB::connection($connection)
                    ->table('permissions')
                    ->join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
                    ->join('role_user', 'permission_role.role_id', '=', 'role_user.role_id')
                    ->where('permissions.name', $permission)
                    ->where('role_user.user_id', $user->id)
                    ->count();
                return $count > 0;
            });
        }
    }

    private function permissionConnection(): ?string
    {
        $tenantDatabase = config('database.connections.tenant.database');

        if ($tenantDatabase) {
            return 'tenant';
        }

        return DB::getDefaultConnection();
    }
}
