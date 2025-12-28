<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftDefinition extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function instances()
    {
        return $this->hasMany(ShiftInstance::class);
    }
}
