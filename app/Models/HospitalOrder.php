<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalOrder extends Model
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
        'schedule_json',
        'start_at',
        'end_at',
        'instructions',
        'status',
        'created_by',
    ];

    protected $casts = [
        'schedule_json' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
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
