<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Procedure extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'code',
        'patient_id',
        'owner_id',
        'patient_snapshot',
        'owner_snapshot',
        'type',
        'name',
        'category',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'location',
        'responsible_vet_name',
        'responsible_vet_license',
        'assistants',
        'preop_notes',
        'intraop_notes',
        'postop_notes',
        'observations',
        'anesthesia_plan',
        'anesthesia_notes',
        'anesthesia_monitoring',
        'pain_management',
        'complications',
        'diagnosis_pre',
        'diagnosis_post',
        'lab_results_summary',
        'consent_document_id',
        'cost_total',
        'currency',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'patient_snapshot' => 'array',
        'owner_snapshot' => 'array',
        'assistants' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function (self $procedure) {
            if (blank($procedure->code)) {
                $procedure->code = self::generateCode($procedure->tenant_id);
            }
        });
    }

    public static function generateCode(?int $tenantId): string
    {
        $query = self::query();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $lastNumber = (int) $query->max('id') + 1;

        return 'PR-' . Str::padLeft((string) $lastNumber, 6, '0');
    }

    public function consentDocument()
    {
        return $this->belongsTo(ConsentDocument::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProcedureAttachment::class);
    }

    public function anesthesiaMedications()
    {
        return $this->hasMany(ProcedureAnesthesiaMedication::class);
    }

    public function events()
    {
        return $this->hasMany(ProcedureEvent::class);
    }
}
