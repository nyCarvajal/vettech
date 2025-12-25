<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReferral extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'historia_clinica_id',
        'patient_id',
        'doctor_name',
        'tests',
        'notes',
        'created_by',
    ];

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
