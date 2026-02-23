<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'document_type',
        'document',
        'departamento_id',
        'municipio_id',
        'address',
        'city',
        'notes',
        'password',
        'email_verified_at',
        'verification_token',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class, 'owner_id');
    }

    public function tutoredPatients()
    {
        return $this->belongsToMany(Patient::class, 'patient_owner', 'owner_id', 'patient_id')
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'departamento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'municipio_id');
    }
}
