<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'invoice_type',
        'prefix',
        'number',
        'full_number',
        'owner_id',
        'user_id',
        'status',
        'currency',
        'issued_at',
        'notes',
        'subtotal',
        'discount_total',
        'tax_total',
        'commission_total',
        'total',
        'paid_total',
        'change_total',
        'electronic_status',
        'cufe',
        'uuid',
        'qr',
        'dian_response',
        'xml_path',
        'pdf_path',
        'sent_at',
        'accepted_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'commission_total' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'change_total' => 'decimal:2',
        'dian_response' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }
}
