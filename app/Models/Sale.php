<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'patient_id', 'created_by', 'total', 'status',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
