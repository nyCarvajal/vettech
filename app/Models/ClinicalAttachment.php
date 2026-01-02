<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalAttachment extends BaseModel
{
    use HasFactory;

    protected $table = 'clinical_attachments';

    protected $fillable = [
        'historia_id',
        'paciente_id',
        'titulo',
        'titulo_limpio',
        'file_type',
        'mime_type',
        'size_bytes',
        'cloudinary_public_id',
        'cloudinary_secure_url',
        'cloudinary_resource_type',
        'cloudinary_format',
        'width',
        'height',
        'duration',
        'created_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'float',
    ];

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
