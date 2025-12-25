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
        'notes',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class, 'owner_id');
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
