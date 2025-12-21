<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'sku', 'unit', 'requires_batch', 'min_stock',
        'sale_price', 'cost_avg', 'active',
    ];

    protected $casts = [
        'requires_batch' => 'boolean',
        'active' => 'boolean',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
