<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public const ROLE_STYLIST = 'stylist';

    protected $table = 'clinicas';

    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function roleLabel(string $role, bool $plural = false): string
    {
        if ($role === self::ROLE_STYLIST) {
            $label = $plural ? $this->trainer_label_plural : $this->trainer_label_singular;
            if ($label) {
                return $label;
            }
        }

        return self::defaultRoleLabel($role, $plural);
    }

    public static function defaultRoleLabel(string $role, bool $plural = false): string
    {
        if ($role === self::ROLE_STYLIST) {
            return $plural ? 'Especialistas' : 'Especialista';
        }

        $base = ucfirst($role);

        return $plural ? $base . 's' : $base;
    }

    public static function resolveForUser($user): ?self
    {
        $peluqueria = $user->relationLoaded('peluqueria') ? $user->peluqueria : null;

        if ($peluqueria instanceof self) {
            return $peluqueria;
        }

        $peluqueriaId = $user->peluqueria_id ?? $user->clinica_id ?? null;

        if ($peluqueriaId) {
            return self::on('mysql')->find($peluqueriaId);
        }

        return null;
    }

    public function resolvedLogoUrl(): string
    {
        if (! empty($this->logo)) {
            return $this->logo;
        }

        return asset('images/logo.png');
    }
}
