<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingSetting extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'pos_prefix',
        'default_tax_rate',
        'default_commission_rate',
        'currency',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:3',
        'default_commission_rate' => 'decimal:3',
    ];
}
