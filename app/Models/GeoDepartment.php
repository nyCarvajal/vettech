<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoDepartment extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $connection = 'mysql';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $fillable = ['id', 'nombre', 'codigo', 'pais_id'];
    protected $appends = ['name'];

    public function municipalities()
    {
        return $this->hasMany(GeoMunicipality::class, 'departamento_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nombre'] ?? null;
    }
}
