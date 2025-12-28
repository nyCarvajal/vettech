<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalConsumption extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'stay_id', 'product_id', 'batch_id', 'qty', 'source', 'created_by',
    ];

    public function stay()
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
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
