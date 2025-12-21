<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function stays()
    {
        return $this->hasMany(HospitalStay::class);
    }
}
