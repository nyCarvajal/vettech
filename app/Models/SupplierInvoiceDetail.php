<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvoiceDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'supplier_invoice_id', 'item_id', 'descripcion', 'cantidad', 'costo_unitario',
        'precio_venta_unitario', 'subtotal', 'es_obsequio', 'afecta_valor',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'precio_venta_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'es_obsequio' => 'boolean',
        'afecta_valor' => 'boolean',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
