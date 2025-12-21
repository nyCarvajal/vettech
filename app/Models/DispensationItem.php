<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispensation_id', 'product_id', 'batch_id', 'qty_dispensed', 'unit_price', 'cost_snapshot',
    ];

    public function dispensation()
    {
        return $this->belongsTo(Dispensation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
