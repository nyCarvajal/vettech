<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsentAttachment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'consent_document_id',
        'title',
        'file_path',
        'mime',
        'size_bytes',
    ];

    public function document()
    {
        return $this->belongsTo(ConsentDocument::class, 'consent_document_id');
    }
}
