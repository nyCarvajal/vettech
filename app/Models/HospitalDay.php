<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HospitalDay extends Model
{
    use HasFactory;

    protected $fillable = ['stay_id', 'date', 'day_number', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(HospitalOrder::class, 'day_id');
    }

    public function administrations(): HasMany
    {
        return $this->hasMany(HospitalAdministration::class, 'day_id');
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(HospitalVital::class, 'day_id');
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(HospitalProgressNote::class, 'day_id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(HospitalCharge::class, 'day_id');
    }
}
