<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalTaskLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'shift_instance_id', 'performed_by', 'performed_at', 'status', 'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(HospitalTask::class, 'task_id');
    }

    public function shiftInstance()
    {
        return $this->belongsTo(ShiftInstance::class);
    }
}
