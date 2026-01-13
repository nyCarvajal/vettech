<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends BaseModel
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'owner_id',
        'species_id',
        'breed_id',
        'nombres',
        'apellidos',
        'sexo',
        'fecha_nacimiento',
        'color',
        'microchip',
        'estado',
        'peso_actual',
        'weight_unit',
        'temperamento',
        'alergias',
        'photo_path',
        'observaciones',
        'whatsapp',
        'email',
        'direccion',
        'ciudad',
        'tipo_documento',
        'numero_documento',
        'age_value',
        'age_unit',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso_actual' => 'decimal:2',
        'age_value' => 'integer',
    ];

    protected $appends = ['display_name', 'edad'];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }

    public function encounters()
    {
        return $this->hasMany(Encounter::class, 'patient_id');
    }

    public function lastEncounter()
    {
        return $this->hasOne(Encounter::class, 'patient_id')->latestOfMany('occurred_at');
    }

    public function hospitalStays()
    {
        return $this->hasMany(HospitalStay::class, 'patient_id');
    }

    public function immunizations()
    {
        return $this->hasMany(PatientImmunization::class, 'paciente_id');
    }

    public function dewormings()
    {
        return $this->hasMany(PatientDeworming::class, 'paciente_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'patient_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function getEdadAttribute(): ?string
    {
        if ($this->age_value !== null) {
            $unit = $this->age_unit === 'months' ? 'mes' : 'año';
            $suffix = $this->age_value === 1 ? '' : 's';

            return $this->age_value . ' ' . $unit . $suffix;
        }

        if (! $this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->age . ' años';
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        $species = strtolower(optional($this->species)->name ?? '');

        return match ($species) {
            'canino' => asset('images/users/perro.png'),
            'felino' => asset('images/users/gato.png'),
            default => asset('images/users/otro.png'),
        };
    }

    public function getPesoFormateadoAttribute(): ?string
    {
        if ($this->peso_actual === null) {
            return null;
        }

        $unit = $this->weight_unit ?? 'kg';
        $value = $unit === 'g'
            ? $this->peso_actual * 1000
            : (float) $this->peso_actual;

        $formatted = number_format($value, $unit === 'g' ? 0 : 2, '.', '');

        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . ' ' . $unit;
    }
}
