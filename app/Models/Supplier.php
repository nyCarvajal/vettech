<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'razon_social',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'celular',
        'email',
        'direccion',
        'ciudad',
        'contacto_principal',
        'observaciones',
        'estado',
        'created_by',
        'updated_by',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
