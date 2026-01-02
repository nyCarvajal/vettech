<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelCertificateVaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_certificate_id',
        'vaccine_name',
        'product_name',
        'batch_lot',
        'applied_at',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'valid_until' => 'date',
    ];

    public function certificate()
    {
        return $this->belongsTo(TravelCertificate::class, 'travel_certificate_id');
    }
}
