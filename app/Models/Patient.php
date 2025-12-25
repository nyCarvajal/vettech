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
        'peso_actual',
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
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso_actual' => 'decimal:2',
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
            'perro' => asset('images/users/perro.png'),
            'gato' => asset('images/users/gato.png'),
            default => asset('images/users/otro.png'),
        };
    }
}
