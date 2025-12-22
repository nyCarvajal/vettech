<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Species extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name'];

    public function breeds()
    {
        return $this->hasMany(Breed::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'species_id');
    }
}
