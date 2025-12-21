<?php

namespace App\Support;

use App\Models\Clinica;
use Illuminate\Support\Facades\Auth;

class RoleLabelResolver
{
    /**
     * Obtiene las etiquetas configuradas para el rol de estilista de una peluquería.
     *
     * @return array{singular: string, plural: string}
     */
    public static function forStylist(?Clinica $clinica = null): array
    {
        $clinica = $clinica ?: optional(Auth::user())->peluqueria;

        if ($clinica) {
            return [
                'singular' => $clinica->roleLabel(Clinica::ROLE_STYLIST),
                'plural' => $clinica->roleLabel(Clinica::ROLE_STYLIST, true),
            ];
        }

        return [
            'singular' => Clinica::defaultRoleLabel(Clinica::ROLE_STYLIST),
            'plural' => Clinica::defaultRoleLabel(Clinica::ROLE_STYLIST, true),
        ];
    }
}
