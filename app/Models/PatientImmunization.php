<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientImmunization extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'consulta_id',
        'applied_at',
        'vaccine_name',
        'contains_rabies',
        'item_id',
        'item_manual',
        'batch_lot',
        'dose',
        'next_due_at',
        'expires_at',
        'vet_user_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'next_due_at' => 'date',
        'expires_at' => 'date',
        'contains_rabies' => 'boolean',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'paciente_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'vet_user_id');
    }

    public function scopeNextDueWithin($query, int $days)
    {
        $limit = Carbon::now()->addDays($days)->toDateString();

        return $query->whereNotNull('next_due_at')->whereDate('next_due_at', '<=', $limit);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('next_due_at')->whereDate('next_due_at', '<', Carbon::today());
    }
}
