<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'supplier_invoice_id', 'fecha_pago', 'valor', 'metodo_pago',
        'origen_fondos', 'caja_id', 'banco_id', 'referencia', 'observaciones',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'valor' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class, 'banco_id');
    }
}
