<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalVital extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'day_id',
        'measured_at',
        'temp',
        'hr',
        'rr',
        'spo2',
        'bp',
        'weight',
        'pain_scale',
        'hydration',
        'mucous',
        'crt',
        'notes',
        'measured_by',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
    ];

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(HospitalDay::class, 'day_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }
}
