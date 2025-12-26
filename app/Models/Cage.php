<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active'];

    public function stays(): HasMany
    {
        return $this->hasMany(HospitalStay::class, 'cage_id');
    }
}
