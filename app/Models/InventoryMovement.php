<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'before_stock',
        'after_stock',
        'reference',
        'notes',
        'related_type',
        'related_id',
        'user_id',
        'occurred_at',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'before_stock' => 'decimal:3',
        'after_stock' => 'decimal:3',
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
