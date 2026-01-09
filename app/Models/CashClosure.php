<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashClosure extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'clinica_id',
        'date',
        'user_id',
        'status',
        'expected_cash',
        'counted_cash',
        'difference',
        'expected_card',
        'counted_card',
        'expected_transfer',
        'counted_transfer',
        'total_expected',
        'total_counted',
        'notes',
        'meta',
    ];

    protected $casts = [
        'date' => 'date',
        'expected_cash' => 'decimal:2',
        'counted_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'expected_card' => 'decimal:2',
        'counted_card' => 'decimal:2',
        'expected_transfer' => 'decimal:2',
        'counted_transfer' => 'decimal:2',
        'total_expected' => 'decimal:2',
        'total_counted' => 'decimal:2',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
