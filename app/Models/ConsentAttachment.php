<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentAttachment extends Model
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
