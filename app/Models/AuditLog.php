<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action', 'entity_type', 'entity_id', 'before_json', 'after_json', 'user_id',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
    ];
}
