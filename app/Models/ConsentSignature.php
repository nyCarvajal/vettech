<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsentSignature extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'consent_document_id',
        'signer_role',
        'signer_name',
        'signer_document',
        'signature_image_path',
        'signed_at',
        'ip_address',
        'user_agent',
        'method',
        'geo_hint',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(ConsentDocument::class, 'consent_document_id');
    }
}
