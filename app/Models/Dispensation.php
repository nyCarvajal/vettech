<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispensation extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id', 'dispensed_by', 'dispensed_at', 'status',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function items()
    {
        return $this->hasMany(DispensationItem::class);
    }

    public function dispenser()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
