<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalStay extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'owner_id',
        'cage_id',
        'admitted_at',
        'discharged_at',
        'status',
        'severity',
        'primary_dx',
        'plan',
        'diet',
        'created_by',
    ];

    protected $casts = [
        'admitted_at' => 'datetime',
        'discharged_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function cage(): BelongsTo
    {
        return $this->belongsTo(Cage::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(HospitalDay::class, 'stay_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(HospitalOrder::class, 'stay_id');
    }

    public function administrations(): HasMany
    {
        return $this->hasMany(HospitalAdministration::class, 'stay_id');
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(HospitalVital::class, 'stay_id');
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(HospitalProgressNote::class, 'stay_id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(HospitalCharge::class, 'stay_id');
    }

    public function getCurrentDayAttribute(): ?HospitalDay
    {
        return $this->days()->orderByDesc('day_number')->first();
    }

    public function daysSinceAdmission(): int
    {
        return Carbon::parse($this->admitted_at)->diffInDays(now()) + 1;
    }
}
