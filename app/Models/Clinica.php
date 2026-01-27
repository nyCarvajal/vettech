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

    protected $casts = [
        'responsable_iva' => 'boolean',
        'dian_enabled' => 'boolean',
        'default_tax_rate' => 'decimal:3',
        'features' => 'array',
        'meta' => 'array',
    ];

    // Valores por defecto para módulos disponibles por plan.
    public const FEATURE_DEFAULTS = [
        'agenda' => true,
        'facturacion_pos' => true,
        'tutores' => true,
        'pacientes' => true,
        'dispensacion' => false,
        'hospitalizacion' => false,
        'cirugia' => true,
        'belleza' => true,
        'consentimientos' => true,
        'plantillas_consentimientos' => true,
        'arqueo_caja' => true,
        'reportes_basicos' => true,
        'reportes_avanzados' => false,
        'config_clinica' => true,
    ];

    public static function featureDefaults(): array
    {
        return self::FEATURE_DEFAULTS;
    }

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
        if (! empty($this->logo_path)) {
            return asset('storage/' . $this->logo_path);
        }

        if (! empty($this->logo)) {
            return $this->logo;
        }

        return asset('images/logo.png');
    }

    public function featureEnabled(string $key, bool $default = true): bool
    {
        $features = $this->features ?? [];

        if (array_key_exists($key, $features)) {
            return (bool) $features[$key];
        }

        return $default;
    }
}
