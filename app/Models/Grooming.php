<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Grooming extends BaseModel
{
    use HasFactory;

    public const STATUS_AGENDADO = 'agendado';
    public const STATUS_EN_PROCESO = 'en_proceso';
    public const STATUS_FINALIZADO = 'finalizado';
    public const STATUS_CANCELADO = 'cancelado';

    protected $fillable = [
        'patient_id',
        'owner_id',
        'scheduled_at',
        'status',
        'needs_pickup',
        'pickup_address',
        'external_deworming',
        'deworming_source',
        'deworming_product_id',
        'deworming_product_name',
        'indications',
        'service_source',
        'service_id',
        'product_service_id',
        'service_price',
        'created_by',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'needs_pickup' => 'boolean',
        'external_deworming' => 'boolean',
        'service_price' => 'decimal:2',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_service_id');
    }

    public function groomingService(): BelongsTo
    {
        return $this->belongsTo(GroomingService::class, 'service_id');
    }

    public function dewormingProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'deworming_product_id');
    }

    public function report(): HasOne
    {
        return $this->hasOne(GroomingReport::class);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_AGENDADO);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_EN_PROCESO);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINALIZADO);
    }

    public function getServiceNameAttribute(): ?string
    {
        return match ($this->service_source) {
            'product' => optional($this->serviceProduct)->name,
            'grooming_service' => optional($this->groomingService)->name,
            default => null,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AGENDADO => 'bg-mint-50 text-mint-700 border border-mint-100',
            self::STATUS_EN_PROCESO => 'bg-amber-50 text-amber-700 border border-amber-100',
            self::STATUS_FINALIZADO => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
            self::STATUS_CANCELADO => 'bg-rose-50 text-rose-700 border border-rose-100',
            default => 'bg-gray-50 text-gray-700 border border-gray-100',
        };
    }
}
