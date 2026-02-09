<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\ExamReferral;
use App\Models\Prescription;
use App\Models\User;
use App\Services\CloudinaryAttachmentService;
use App\Services\WhatsApp\OneMsgClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentSendController extends Controller
{
    public function sendPrescription(
        Prescription $prescription,
        OneMsgClient $client,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ): RedirectResponse
    {
        $prescription->load(['historiaClinica.paciente.owner']);

        $phone = $this->resolvePhone($prescription->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $filename = 'recetario-' . $prescription->id . '.pdf';
        $url = $this->uploadPrescriptionToCloudinary($prescription, $filename, $cloudinaryAttachmentService);
        $caption = $this->prescriptionCaption($prescription);

        $template = config('services.onemsg.templates.recetario');
        if ($template) {
            $client->sendTemplate(
                $phone,
                $this->documentTemplatePayload(
                    $template,
                    $url,
                    $filename,
                    $this->prescriptionBodyParams($prescription)
                )
            );
        } else {
            $client->sendFile($phone, $url, $filename, $caption);
        }

        return back()->with('success', 'Recetario enviado por WhatsApp.');
    }

    public function sendExamReferral(
        ExamReferral $examReferral,
        OneMsgClient $client,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ): RedirectResponse
    {
        $examReferral->load(['historiaClinica.paciente.owner']);

        $phone = $this->resolvePhone($examReferral->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $filename = 'remision-' . $examReferral->id . '.pdf';
        $url = $this->uploadExamReferralToCloudinary($examReferral, $filename, $cloudinaryAttachmentService);
        $caption = $this->examReferralCaption($examReferral);

        $template = config('services.onemsg.templates.remision');
        if ($template) {
            $client->sendTemplate(
                $phone,
                $this->documentTemplatePayload(
                    $template,
                    $url,
                    $filename,
                    $this->examReferralBodyParams($examReferral)
                )
            );
        } else {
            $client->sendFile($phone, $url, $filename, $caption);
        }

        return back()->with('success', 'Remisión enviada por WhatsApp.');
    }

    private function resolvePhone($paciente): ?string
    {
        if (! $paciente) {
            return null;
        }

        $owner = $paciente->owner;

        return $paciente->whatsapp
            ?: $owner?->whatsapp
            ?: $owner?->phone
            ?: $paciente?->acompanante_contacto;
    }

    private function prescriptionCaption(Prescription $prescription): string
    {
        $paciente = $prescription->historiaClinica?->paciente;
        $pacienteNombre = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? '')) ?: 'tu mascota';
        $mensaje = "Recetario de {$pacienteNombre}.";
        $mensaje .= "\nGracias por confiar en nosotros.";

        return $mensaje;
    }

    private function examReferralCaption(ExamReferral $examReferral): string
    {
        $paciente = $examReferral->historiaClinica?->paciente;
        $pacienteNombre = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? '')) ?: 'tu mascota';
        $mensaje = "Remisión de exámenes para {$pacienteNombre}.";
        if ($examReferral->doctor_name) {
            $mensaje .= "\nDr(a). {$examReferral->doctor_name}.";
        }
        $mensaje .= "\nSi tienes dudas, estamos atentos.";

        return $mensaje;
    }

    private function uploadPrescriptionToCloudinary(
        Prescription $prescription,
        string $filename,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ): string {
        $prescription->load([
            'items.product',
            'historiaClinica.paciente.owner',
            'historiaClinica.paciente.species',
            'historiaClinica.paciente.breed',
        ]);

        $professional = $this->fetchProfessionalById($prescription->professional_id);
        $prescription->setRelation('professional', $professional);

        $pdf = Pdf::loadView('historias_clinicas.recetario_pdf', compact('prescription'))
            ->setPaper('letter');

        $historiaClinica = $prescription->historiaClinica;

        return $this->uploadPdfToCloudinary(
            $pdf->output(),
            $filename,
            $historiaClinica?->paciente_id,
            $historiaClinica?->id,
            'recetario-' . $prescription->id,
            $cloudinaryAttachmentService
        );
    }

    private function uploadExamReferralToCloudinary(
        ExamReferral $examReferral,
        string $filename,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ): string {
        $examReferral->load(['historiaClinica.paciente', 'author']);

        $pdf = Pdf::loadView('historias_clinicas.remision_pdf', compact('examReferral'))
            ->setPaper('letter');

        $historiaClinica = $examReferral->historiaClinica;

        return $this->uploadPdfToCloudinary(
            $pdf->output(),
            $filename,
            $historiaClinica?->paciente_id,
            $historiaClinica?->id,
            'remision-' . $examReferral->id,
            $cloudinaryAttachmentService
        );
    }

    private function uploadPdfToCloudinary(
        string $pdfContent,
        string $filename,
        ?int $pacienteId,
        ?int $historiaId,
        string $publicIdBase,
        CloudinaryAttachmentService $cloudinaryAttachmentService
    ): string {
        $tenantKey = function_exists('tenant') ? tenant('id') : null;
        if (! $tenantKey && app()->bound('tenancy')) {
            $tenantKey = optional(app('tenancy')->tenant)->getTenantKey();
        }

        $tenantKey = $tenantKey ?? config('database.connections.tenant.database') ?? 'tenant';
        $folder = $cloudinaryAttachmentService->buildFolderPath(
            $tenantKey,
            $pacienteId ?? 0,
            $historiaId ?? 0
        );

        $tmpPath = tempnam(sys_get_temp_dir(), 'pdf-');
        file_put_contents($tmpPath, $pdfContent);

        $uploadedFile = new UploadedFile(
            $tmpPath,
            $filename,
            'application/pdf',
            null,
            true
        );

        try {
            $upload = $cloudinaryAttachmentService->upload(
                $uploadedFile,
                $folder,
                'pdf',
                $publicIdBase . '-' . Str::random(6),
                $filename
            );
        } finally {
            @unlink($tmpPath);
        }

        return $upload['secure_url'] ?? '';
    }

    private function prescriptionBodyParams(Prescription $prescription): array
    {
        $paciente = $prescription->historiaClinica?->paciente;
        $pacienteNombre = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? '')) ?: 'tu mascota';

        return [$pacienteNombre];
    }

    private function examReferralBodyParams(ExamReferral $examReferral): array
    {
        $paciente = $examReferral->historiaClinica?->paciente;
        $pacienteNombre = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? '')) ?: 'tu mascota';
        $doctorLine = $examReferral->doctor_name ? "Dr(a). {$examReferral->doctor_name}.\n" : '';

        return [$pacienteNombre, $doctorLine];
    }

    private function documentTemplatePayload(
        string $template,
        string $fileUrl,
        string $filename,
        array $bodyParams
    ): array {
        $namespace = config('services.onemsg.namespace');
        $langCode = config('services.onemsg.lang.default', 'es');

        $bodyParameters = collect($bodyParams)
            ->map(fn ($value) => [
                'type' => 'text',
                'text' => (string) $value,
            ])
            ->values()
            ->toArray();

        return [
            'namespace' => $namespace,
            'template' => $template,
            'language' => [
                'policy' => 'deterministic',
                'code' => $langCode,
            ],
            'params' => [
                [
                    'type' => 'header',
                    'parameters' => [
                        [
                            'type' => 'document',
                            'document' => [
                                'link' => $fileUrl,
                                'filename' => $filename,
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'body',
                    'parameters' => $bodyParameters,
                ],
            ],
        ];
    }

    private function fetchProfessionalById(?int $professionalId): ?User
    {
        if (! $professionalId) {
            return null;
        }

        return User::on('mysql')
            ->from('usuarios')
            ->whereKey($professionalId)
            ->first();
    }
}
