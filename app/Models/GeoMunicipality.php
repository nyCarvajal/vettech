<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoMunicipality extends Model
{
    use HasFactory;

    protected $fillable = ['geo_department_id', 'name', 'code'];

    public function department()
    {
        return $this->belongsTo(GeoDepartment::class, 'geo_department_id');
    }
}
