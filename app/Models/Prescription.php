<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'encounter_id', 'historia_clinica_id', 'patient_id', 'professional_id', 'status',
    ];

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function professional()
    {
        $relation = $this->belongsTo(User::class, 'professional_id');
        $relation->getRelated()->setConnection('mysql');
        $relation->getRelated()->setTable('usuarios');

        return $relation;
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class);
    }

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }
}
