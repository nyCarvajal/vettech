<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierInvoice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'numero_factura', 'fecha_factura', 'fecha_vencimiento',
        'subtotal', 'descuento', 'impuestos', 'total', 'total_pagado', 'saldo_pendiente',
        'estado_pago', 'estado', 'observaciones', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SupplierInvoiceDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->pendientes()->whereDate('fecha_vencimiento', '<', now()->toDateString());
    }

    public function scopePorVencer(Builder $query, int $dias = 5): Builder
    {
        return $query->pendientes()->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays($dias)->toDateString()]);
    }
}
