<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Clinica;
use Stancl\Tenancy\Tenancy;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles;
    use HasFactory, Notifiable;

    protected $connection = 'mysql';

    // Modelo de autenticación apuntando a la tabla de usuarios
    protected $table = 'users';

    protected $casts = [
        'db' => 'string',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'nivel',
        'tipo_identificacion',
        'numero_identificacion',
        'direccion',
        'whatsapp',
        'ciudad',
        'password',
        'clinica_id',
        'role',
        'color',
        'firma_medica_texto',
        'firma_medica_url',
        'firma_medica_public_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): ?string
    {
        $firstName = $this->nombres ?? $this->nombre ?? '';
        $name = trim($firstName . ' ' . ($this->apellidos ?? ''));

        return $name !== '' ? $name : null;
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
	// Aquí definimos la relación "item" (o como prefieras nombrarla):
    public function peluqueria()
    {
        // 'producto' es la FK en 'ventas' que apunta a 'id' de 'items'
        return $this->belongsTo(Clinica::class, 'clinica_id', 'id');
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id', 'id');
    }
}
