<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalStay extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'cage_id', 'admitted_at', 'discharged_at', 'status', 'severity', 'diagnosis', 'plan', 'diet', 'created_by',
    ];

    protected $casts = [
        'admitted_at' => 'datetime',
        'discharged_at' => 'datetime',
    ];

    public function cage()
    {
        return $this->belongsTo(Cage::class);
    }

    public function tasks()
    {
        return $this->hasMany(HospitalTask::class, 'stay_id');
    }

    public function consumptions()
    {
        return $this->hasMany(HospitalConsumption::class, 'stay_id');
    }
}
