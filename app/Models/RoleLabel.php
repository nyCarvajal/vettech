<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Clinica;

class RoleLabel extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'peluqueria_id',
        'role',
        'singular',
        'plural',
    ];

    public function peluqueria(): BelongsTo
    {
        return $this->belongsTo(Clinica::class, 'peluqueria_id');
    }
}
