<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalTask extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'stay_id', 'category', 'title', 'instructions', 'times_json', 'start_at', 'end_at', 'created_by',
    ];

    protected $casts = [
        'times_json' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function stay()
    {
        return $this->belongsTo(HospitalStay::class, 'stay_id');
    }

    public function logs()
    {
        return $this->hasMany(HospitalTaskLog::class, 'task_id');
    }
}
