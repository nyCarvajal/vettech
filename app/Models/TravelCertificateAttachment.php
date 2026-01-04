<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelCertificateAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_certificate_id',
        'title',
        'file_path',
        'mime',
        'size_bytes',
    ];

    public function certificate()
    {
        return $this->belongsTo(TravelCertificate::class, 'travel_certificate_id');
    }
}
