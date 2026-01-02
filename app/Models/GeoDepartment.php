<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function municipalities()
    {
        return $this->hasMany(GeoMunicipality::class);
    }
}
