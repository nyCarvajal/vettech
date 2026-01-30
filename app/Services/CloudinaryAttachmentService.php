<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary as CloudinaryFacade;
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

    public function upload(
        UploadedFile $file,
        string $folder,
        string $fileType,
        ?string $publicId = null,
        ?string $filenameOverride = null
    ): array
    {
        $this->ensureCloudinaryConfigured();
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
            'access_mode' => $fileType === 'pdf' ? 'public' : null,
            'transformation' => $transformation,
            'format' => $fileType === 'image' ? 'webp' : null,
            'overwrite' => false,
        ]);

        $upload = CloudinaryFacade::uploadApi()->upload($file->getRealPath(), $options);

        return $this->normalizeUploadResponse($upload);
    }

    public function delete(string $publicId, string $resourceType = 'image'): void
    {
        try {
            $this->ensureCloudinaryConfigured();
            CloudinaryFacade::uploadApi()->destroy($publicId, ['resource_type' => $resourceType]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function ensureCloudinaryConfigured(): void
    {
        $cloudConfig = config('cloudinary.cloud');

        if (! is_array($cloudConfig)) {
            throw new \RuntimeException('Cloudinary configuration missing. Set CLOUDINARY_URL or CLOUDINARY_API_KEY/SECRET.');
        }

        if (empty($cloudConfig['cloud_name']) || empty($cloudConfig['api_key']) || empty($cloudConfig['api_secret'])) {
            throw new \RuntimeException('Cloudinary credentials missing. Set CLOUDINARY_URL or CLOUDINARY_API_KEY/SECRET.');
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

    private function normalizeUploadResponse($upload): array
    {
        if (is_array($upload)) {
            return $upload;
        }

        if (is_object($upload)) {
            if (method_exists($upload, 'toArray')) {
                return $upload->toArray();
            }

            if (method_exists($upload, 'jsonSerialize')) {
                return (array) $upload->jsonSerialize();
            }
        }

        return (array) $upload;
    }

    
}
