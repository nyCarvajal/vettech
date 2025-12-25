<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'encounter_id', 'patient_id', 'professional_id', 'status',
    ];

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class);
    }
}
