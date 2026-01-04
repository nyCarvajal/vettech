<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoMunicipality extends Model
{
    use HasFactory;

    protected $table = 'municipios';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $fillable = ['id', 'departamento_id', 'codigo', 'nombre'];
    protected $appends = ['name'];

    public function department()
    {
        return $this->belongsTo(GeoDepartment::class, 'departamento_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nombre'] ?? null;
    }
}
