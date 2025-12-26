<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalAdministration extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'stay_id',
        'day_id',
        'scheduled_time',
        'administered_at',
        'dose_given',
        'status',
        'notes',
        'administered_by',
    ];

    protected $casts = [
        'administered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(HospitalOrder::class, 'order_id');
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(HospitalDay::class, 'day_id');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
