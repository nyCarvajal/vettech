<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsentDocument extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'tenant_id',
        'code',
        'status',
        'template_id',
        'owner_id',
        'pet_id',
        'owner_snapshot',
        'patient_snapshot',
        'pet_snapshot',
        'merged_body_html',
        'merged_plain_text',
        'created_by',
        'signed_at',
        'canceled_reason',
    ];

    protected $casts = [
        'owner_snapshot' => 'array',
        'pet_snapshot' => 'array',
        'signed_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(ConsentTemplate::class, 'template_id');
    }

    public function signatures()
    {
        return $this->hasMany(ConsentSignature::class);
    }

    public function publicLinks()
    {
        return $this->hasMany(ConsentPublicLink::class);
    }

    public function attachments()
    {
        return $this->hasMany(ConsentAttachment::class);
    }
}
