<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HistoriaClinica;

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
    ];

    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class);
    }
}
