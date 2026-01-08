<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;

class CloudinaryAttachmentService
{
    public function buildFolderPath(string $tenantKey, int $pacienteId, int $historiaId): string
    {
        $tenantSlug = Str::slug($tenantKey);

        return "tenants/{$tenantSlug}/pacientes/{$pacienteId}/historias/{$historiaId}/adjuntos";
    }

    public function upload(UploadedFile $file, string $folder, string $fileType, ?string $publicId = null): array
    {
        $resourceType = $this->resourceTypeFromFileType($fileType);

        $transformation = null;

        if ($fileType === 'image') {
            $transformation = [['quality' => 'auto', 'fetch_format' => 'auto', 'width' => 2000, 'crop' => 'limit']];
        }

        if ($fileType === 'video') {
            $transformation = [['quality' => 'auto:eco']];
        }

        $options = array_filter([
            'folder' => $folder,
            'resource_type' => $resourceType,
            'public_id' => $publicId,
            'transformation' => $transformation,
            'format' => $fileType === 'image' ? 'webp' : null,
            'overwrite' => false,
        ]);

        $upload = Cloudinary::uploadApi()->upload($file->getRealPath(), $options);

        return $upload;
    }

    public function delete(string $publicId, string $resourceType = 'image'): void
    {
        try {
            Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => $resourceType]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function resourceTypeFromFileType(string $fileType): string
    {
        return match ($fileType) {
            'video' => 'video',
            'pdf' => 'raw',
            default => 'image',
        };
    }
}
