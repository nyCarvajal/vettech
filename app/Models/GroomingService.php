<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroomingService extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration_minutes',
        'default_price',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'default_price' => 'decimal:2',
    ];
}
