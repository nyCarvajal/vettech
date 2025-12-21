<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class HistoriaClinicaDiagnostico extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'historia_clinica_id',
        'codigo',
        'descripcion',
        'confirmado',
    ];

    protected $casts = [
        'confirmado' => 'boolean',
    ];

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

    public function historiaClinica()
    {
        return $this->belongsTo(HistoriaClinica::class);
    }
}
