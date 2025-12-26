<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroomingReport extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'grooming_id',
        'fleas',
        'ticks',
        'skin_issue',
        'ear_issue',
        'observations',
        'recommendations',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'fleas' => 'boolean',
        'ticks' => 'boolean',
        'skin_issue' => 'boolean',
        'ear_issue' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function grooming(): BelongsTo
    {
        return $this->belongsTo(Grooming::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
