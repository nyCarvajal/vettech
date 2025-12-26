<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroomingMedia extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'grooming_id',
        'type',
        'file_path',
        'uploaded_by',
    ];

    public function grooming(): BelongsTo
    {
        return $this->belongsTo(Grooming::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
