<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelCertificateDeworming extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_certificate_id',
        'kind',
        'product_name',
        'active_ingredient',
        'batch_lot',
        'applied_at',
        'notes',
    ];

    protected $casts = [
        'applied_at' => 'date',
    ];

    public function certificate()
    {
        return $this->belongsTo(TravelCertificate::class, 'travel_certificate_id');
    }
}
