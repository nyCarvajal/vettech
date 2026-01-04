<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'procedure_id',
        'event_type',
        'payload',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
