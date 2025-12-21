<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;

class HistoriaClinica extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    public function resolveRouteBinding($value, $field = null)
    {
        $dbName = optional(optional(Auth::user())->peluqueria)->db;

        if ($dbName) {
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        $field = $field ?: $this->getRouteKeyName();

        return $this->on('tenant')->where($field, $value)->firstOrFail();
    }

    protected $fillable = [
        'paciente_id',
        'estado',
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes_farmacologicos',
        'antecedentes_patologicos',
        'antecedentes_toxicologicos',
        'antecedentes_alergicos',
        'antecedentes_inmunologicos',
        'antecedentes_quirurgicos',
        'antecedentes_ginecologicos',
        'antecedentes_familiares',
        'revision_sistemas',
        'frecuencia_cardiaca',
        'tension_arterial',
        'saturacion_oxigeno',
        'frecuencia_respiratoria',
        'examen_cabeza_cuello',
        'examen_torax',
        'examen_corazon',
        'examen_mama',
        'examen_abdomen',
        'examen_genitales',
        'examen_neurologico',
        'examen_extremidades',
        'examen_piel',
        'analisis',
        'plan_procedimientos',
        'plan_medicamentos',
        'mipres_url',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function paraclinicos()
    {
        return $this->hasMany(HistoriaClinicaParaclinico::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(HistoriaClinicaDiagnostico::class);
    }
}
