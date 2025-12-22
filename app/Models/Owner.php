<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'document',
        'address',
        'notes',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class, 'owner_id');
    }
}
