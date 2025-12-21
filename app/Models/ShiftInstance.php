<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftInstance extends Model
{
    use HasFactory;

    protected $fillable = ['shift_definition_id', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function definition()
    {
        return $this->belongsTo(ShiftDefinition::class, 'shift_definition_id');
    }
}
