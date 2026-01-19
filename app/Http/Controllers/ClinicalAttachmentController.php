<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClinicalAttachmentRequest;
use App\Models\ClinicalAttachment;
use App\Models\HistoriaClinica;
use App\Services\CloudinaryAttachmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ClinicalAttachmentController extends Controller
{
    public function index(HistoriaClinica $historiaClinica)
    {
        $attachments = $historiaClinica->adjuntos()->get();

        return response()->json($attachments);
    }

    public function store(
        ClinicalAttachmentRequest $request,
        HistoriaClinica $historiaClinica,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ) {
        $files = $request->file('files', []);
        $sanitizedTitle = $request->sanitizedTitle();
        $tenantKey = function_exists('tenant') ? tenant('id') : null;
        if (! $tenantKey && app()->bound('tenancy')) {
            $tenantKey = optional(app('tenancy')->tenant)->getTenantKey();
        }

        $tenantKey = $tenantKey ?? config('database.connections.tenant.database');
        $folder = $cloudinaryAttachmentService->buildFolderPath(
            $tenantKey ?? 'tenant',
            $historiaClinica->paciente_id,
            $historiaClinica->id
        );

        $created = [];

        DB::connection('tenant')->transaction(function () use (
            $files,
            $historiaClinica,
            $sanitizedTitle,
            $request,
            $cloudinaryAttachmentService,
            $folder,
            &$created
        ) {
            foreach ($files as $index => $file) {
                $mimeType = $file->getMimeType();
                $fileType = $request->fileTypeFromMime($mimeType);
                $uniqueTitle = $this->uniqueTitle($historiaClinica, $sanitizedTitle, $index);
                if ($fileType === 'pdf') {
                    $publicId = $uniqueTitle;
                } else {
                    $publicId = $uniqueTitle . '-' . Str::random(6);
                }

                try {
                    $uploadResult = $cloudinaryAttachmentService->upload(
                        $file,
                        $folder,
                        $fileType,
                        $publicId,
                        $filenameOverride
                    );
                } catch (Throwable $exception) {
                    Log::error('Fallo la subida a Cloudinary', [
                        'historia_id' => $historiaClinica->id,
                        'exception' => $exception->getMessage(),
                    ]);

                    throw $exception;
                }

                $created[] = ClinicalAttachment::create([
                    'historia_id' => $historiaClinica->id,
                    'paciente_id' => $historiaClinica->paciente_id,
                    'titulo' => $request->input('titulo'),
                    'titulo_limpio' => $uniqueTitle,
                    'file_type' => $fileType,
                    'mime_type' => $mimeType,
                    'size_bytes' => $file->getSize() ?: 0,
                    'cloudinary_public_id' => $uploadResult['public_id'] ?? $publicId,
                    'cloudinary_secure_url' => $uploadResult['secure_url'] ?? '',
                    'cloudinary_resource_type' => $uploadResult['resource_type'] ?? $fileType,
                    'cloudinary_format' => $uploadResult['format'] ?? null,
                    'width' => $uploadResult['width'] ?? null,
                    'height' => $uploadResult['height'] ?? null,
                    'duration' => $uploadResult['duration'] ?? null,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return back()->with('success', 'Adjunto(s) cargado(s) correctamente.');
    }

    public function destroy(ClinicalAttachment $attachment, CloudinaryAttachmentService $cloudinaryAttachmentService)
    {
        $resourceType = $attachment->cloudinary_resource_type ?? 'image';
        $publicId = $attachment->cloudinary_public_id;

        DB::connection('tenant')->transaction(function () use ($attachment, $cloudinaryAttachmentService, $resourceType, $publicId) {
            $attachment->delete();

            try {
                $cloudinaryAttachmentService->delete($publicId, $resourceType);
            } catch (Throwable $exception) {
                Log::warning('No se pudo eliminar el adjunto en Cloudinary', [
                    'attachment_id' => $attachment->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        });

        return back()->with('success', 'Adjunto eliminado.');
    }

    private function uniqueTitle(HistoriaClinica $historiaClinica, string $baseTitle, int $offset = 0): string
    {
        $cleanBase = $baseTitle;

        if ($offset > 0) {
            $cleanBase .= '-' . ($offset + 1);
        }

        $existing = $historiaClinica->adjuntos()
            ->where('titulo_limpio', $cleanBase)
            ->exists();

        if (! $existing) {
            return $cleanBase;
        }

        $suffix = 1;
        while (true) {
            $candidate = $cleanBase . '-' . $suffix;
            $exists = $historiaClinica->adjuntos()
                ->where('titulo_limpio', $candidate)
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $suffix++;
        }
    }
}
