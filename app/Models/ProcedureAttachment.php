<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'procedure_id',
        'title',
        'file_path',
        'mime',
        'size_bytes',
        'uploaded_by',
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }
}
