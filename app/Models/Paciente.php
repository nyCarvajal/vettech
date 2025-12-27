<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HistoriaClinica;
use App\Models\Owner;
use App\Models\Species;
use App\Models\Breed;

class Paciente extends BaseModel
{
    use HasFactory;

    protected $connection = 'tenant';
    protected $table = 'pacientes';

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'direccion',
        'ciudad',
        'whatsapp',
        'email',
        'sexo',
        'fecha_nacimiento',
        'alergias',
        'acompanante',
        'acompanante_contacto',
        'observaciones',
        'proximo_control_at',
    ];

    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class, 'breed_id');
    }

    public function getEdadAttribute(): ?string
    {
        if (! $this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->age . ' años';
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
