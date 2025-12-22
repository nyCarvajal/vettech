<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Encounter extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'occurred_at',
        'professional',
        'motivo',
        'diagnostico',
        'plan',
        'peso',
        'temperatura',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'peso' => 'decimal:2',
        'temperatura' => 'decimal:1',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
