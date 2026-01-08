<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FollowupAttachment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'followup_id',
        'title',
        'file_path',
        'mime',
        'size_bytes',
        'uploaded_by',
    ];

    public function followup()
    {
        return $this->belongsTo(Followup::class);
    }
}
