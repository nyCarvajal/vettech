<?php

namespace App\Models;

use Cloudinary\Cloudinary as CloudinarySdk;
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

    public function signedViewUrl(): string
    {
        return $this->buildSignedUrl();
    }

    public function signedDownloadUrl(string $filename): string
    {
        return $this->buildSignedUrl($filename);
    }

    private function buildSignedUrl(?string $downloadFilename = null): string
    {
        if (! $this->cloudinary_public_id) {
            return (string) $this->cloudinary_secure_url;
        }

        $cloudinary = new CloudinarySdk(config('cloudinary'));
        $isPdf = $this->file_type === 'pdf';
        $asset = $isPdf
            ? $cloudinary->raw($this->cloudinary_public_id)
            : $cloudinary->image($this->cloudinary_public_id);
        $asset = $asset->deliveryType($isPdf ? 'public' : 'authenticated');
        $version = $this->extractCloudinaryVersion();

        $format = $this->cloudinary_format ?? ($isPdf ? 'pdf' : null);
        if ($format && ! $isPdf) {
            $asset = $asset->format($format);
        }

        if ($downloadFilename && ! $isPdf) {
            $asset = $asset->addFlag('attachment:' . $downloadFilename);
        }

        if ($version) {
            $asset = $asset->version($version);
        }

        $asset = $asset->signUrl(true);

        return (string) $asset->toUrl();
    }

    private function extractCloudinaryVersion(): ?int
    {
        if (! $this->cloudinary_secure_url) {
            return null;
        }

        if (preg_match('#/v(\\d+)/#', $this->cloudinary_secure_url, $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
