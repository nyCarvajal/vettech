<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'owner_id',
        'patient_snapshot',
        'owner_snapshot',
        'template_id',
        'status',
        'signed_at',
    ];
}
