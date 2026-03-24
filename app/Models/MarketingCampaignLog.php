<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaignLog extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'owner_id',
        'campaign_type',
        'message',
        'sent_at',
        'status',
        'response',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
