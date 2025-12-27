<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalProgressNote extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'stay_id',
        'day_id',
        'logged_at',
        'shift',
        'content',
        'author_id',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(HospitalDay::class, 'day_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
