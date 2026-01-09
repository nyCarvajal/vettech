<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'paid_at',
        'payment_method',
        'user_id',
        'owner_id',
        'tenant_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];
}
