<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\ExamReferral;
use App\Models\Prescription;
use App\Services\WhatsApp\OneMsgClient;
use Illuminate\Http\RedirectResponse;

class DocumentSendController extends Controller
{
    public function sendPrescription(Prescription $prescription, OneMsgClient $client): RedirectResponse
    {
        $prescription->load(['historiaClinica.paciente.owner']);

        $phone = $this->resolvePhone($prescription->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $url = route('historias-clinicas.recetarios.print', $prescription);
        $filename = 'recetario-' . $prescription->id . '.pdf';
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

    public function sendExamReferral(ExamReferral $examReferral, OneMsgClient $client): RedirectResponse
    {
        $examReferral->load(['historiaClinica.paciente.owner']);

        $phone = $this->resolvePhone($examReferral->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $url = route('historias-clinicas.remisiones.print', $examReferral);
        $filename = 'remision-' . $examReferral->id . '.pdf';
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
}
