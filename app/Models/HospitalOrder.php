<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalOrder extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'day_id',
        'type',
        'source',
        'product_id',
        'manual_name',
        'dose',
        'route',
        'frequency',
        'frequency_type',
        'frequency_value',
        'schedule_json',
        'start_at',
        'end_at',
        'duration_days',
        'next_due_at',
        'last_applied_at',
        'instructions',
        'status',
        'created_by',
    ];

    protected $casts = [
        'schedule_json' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'next_due_at' => 'datetime',
        'last_applied_at' => 'datetime',
    ];

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(HospitalDay::class, 'day_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function administrations(): HasMany
    {
        return $this->hasMany(HospitalAdministration::class, 'order_id');
    }
}
