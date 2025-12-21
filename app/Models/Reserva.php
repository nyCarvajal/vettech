<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Paciente;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'duracion',
        'paciente_id',
        'entrenador_id',
        'tipocita_id',
        'estado',
        'tipo',
        'nota_cliente',
        'modalidad',
        'visita_tipo',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function entrenador()
    {
        return $this->belongsTo(User::class, 'entrenador_id');
    }

    public function tipocita()
    {
        return $this->belongsTo(Tipocita::class);
    }

    public function getFinAttribute(): ?Carbon
    {
        if (! $this->fecha) {
            return null;
        }

        return $this->fecha->copy()->addMinutes($this->duracion ?? 60);
    }
}
