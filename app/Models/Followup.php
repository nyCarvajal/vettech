<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Followup extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'code',
        'patient_id',
        'owner_id',
        'consultation_id',
        'patient_snapshot',
        'owner_snapshot',
        'followup_at',
        'performed_by',
        'performed_by_license',
        'reason',
        'improved_status',
        'improved_score',
        'observations',
        'plan',
        'next_followup_at',
    ];

    protected $casts = [
        'patient_snapshot' => 'array',
        'owner_snapshot' => 'array',
        'followup_at' => 'datetime',
        'next_followup_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $followup) {
            if (blank($followup->code)) {
                $followup->code = self::generateCode($followup->tenant_id);
            }
        });
    }

    public static function generateCode(?int $tenantId = null): string
    {
        $query = self::query();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $lastNumber = (int) $query->max('id') + 1;

        return 'CT-' . Str::padLeft((string) $lastNumber, 6, '0');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Encounter::class, 'consultation_id');
    }

    public function vitals()
    {
        return $this->hasOne(FollowupVitals::class);
    }

    public function attachments()
    {
        return $this->hasMany(FollowupAttachment::class);
    }
}
