<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DianResolution extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'range_start',
        'range_end',
        'current_number',
        'valid_from',
        'valid_until',
        'resolution_number',
        'technical_key',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];
}
