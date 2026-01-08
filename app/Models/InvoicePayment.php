<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'method',
        'amount',
        'received',
        'change',
        'reference',
        'paid_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received' => 'decimal:2',
        'change' => 'decimal:2',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
