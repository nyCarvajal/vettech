<?php

namespace App\Support;

use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClinicaActual
{
    public static function get(): Clinica
    {
        $user = Auth::user();

        if ($user) {
            $clinica = Clinica::resolveForUser($user) ?? $user->clinica;

            if ($clinica instanceof Clinica) {
                return $clinica;
            }
        }

        return Clinica::on(self::connectionName())->firstOrFail();
    }

    public static function connectionName(): string
    {
        $tenantDatabase = config('database.connections.tenant.database');

        if ($tenantDatabase) {
            return 'tenant';
        }

        return DB::getDefaultConnection();
    }
}
