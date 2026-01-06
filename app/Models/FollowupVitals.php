<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowupVitals extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'followup_id',
        'temperature_c',
        'heart_rate_bpm',
        'respiratory_rate_rpm',
        'weight_kg',
        'hydration',
        'mucous_membranes',
        'capillary_refill_time_sec',
        'pain_score_0_10',
        'blood_pressure_sys',
        'blood_pressure_dia',
        'blood_pressure_map',
        'o2_saturation_percent',
        'notes',
    ];

    public function followup()
    {
        return $this->belongsTo(Followup::class);
    }
}
