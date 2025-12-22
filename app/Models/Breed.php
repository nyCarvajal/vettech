<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Breed extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'species_id'];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'breed_id');
    }
}
