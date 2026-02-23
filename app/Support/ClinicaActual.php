<?php

namespace App\Support;

use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;

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
        return 'mysql';
    }
}
