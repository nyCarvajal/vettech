<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureAnesthesiaMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'procedure_id',
        'drug_name',
        'dose',
        'dose_unit',
        'route',
        'frequency',
        'notes',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
