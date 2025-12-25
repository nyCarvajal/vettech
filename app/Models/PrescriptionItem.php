<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrescriptionItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'prescription_id', 'product_id', 'dose', 'frequency', 'duration_days', 'instructions', 'qty_requested',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
