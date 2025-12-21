<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandoffNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'stay_id', 'shift_instance_id', 'author_id', 'summary', 'pending', 'alerts',
    ];

    public function stay()
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function shiftInstance()
    {
        return $this->belongsTo(ShiftInstance::class);
    }
}
