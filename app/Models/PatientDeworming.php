<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDeworming extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'consulta_id',
        'type',
        'applied_at',
        'item_id',
        'item_manual',
        'dose',
        'route',
        'duration_days',
        'next_due_at',
        'vet_user_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'applied_at' => 'date',
        'next_due_at' => 'date',
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
