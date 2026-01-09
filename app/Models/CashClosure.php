<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashClosure extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'expected_cash',
        'counted_cash',
        'difference',
        'notes',
        'tenant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'expected_cash' => 'decimal:2',
        'counted_cash' => 'decimal:2',
        'difference' => 'decimal:2',
    ];
}
