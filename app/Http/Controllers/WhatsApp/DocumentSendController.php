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
        $prescription->load(['historiaClinica.paciente.owner', 'professional']);

        $phone = $this->resolvePhone($prescription->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $url = route('historias-clinicas.recetarios.print', $prescription);
        $filename = 'recetario-' . $prescription->id . '.pdf';
        $caption = $this->prescriptionCaption($prescription);

        $client->sendFile($phone, $url, $filename, $caption);

        return back()->with('success', 'Recetario enviado por WhatsApp.');
    }

    public function sendExamReferral(ExamReferral $examReferral, OneMsgClient $client): RedirectResponse
    {
        $examReferral->load(['historiaClinica.paciente.owner', 'author']);

        $phone = $this->resolvePhone($examReferral->historiaClinica?->paciente);

        if (! $phone) {
            return back()->with('error', 'No hay WhatsApp del tutor registrado.');
        }

        $url = route('historias-clinicas.remisiones.print', $examReferral);
        $filename = 'remision-' . $examReferral->id . '.pdf';
        $caption = $this->examReferralCaption($examReferral);

        $client->sendFile($phone, $url, $filename, $caption);

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
        $profesional = $prescription->professional?->name;

        $mensaje = "Recetario de {$pacienteNombre}.";

        if ($profesional) {
            $mensaje .= "\nProfesional: {$profesional}.";
        }

        $mensaje .= "\nGracias por confiar en nosotros.";

        return $mensaje;
    }

    private function examReferralCaption(ExamReferral $examReferral): string
    {
        $paciente = $examReferral->historiaClinica?->paciente;
        $pacienteNombre = trim(($paciente?->nombres ?? '') . ' ' . ($paciente?->apellidos ?? '')) ?: 'tu mascota';
        $doctor = $examReferral->author?->name ?? $examReferral->doctor_name;

        $mensaje = "Remisión de exámenes para {$pacienteNombre}.";

        if ($doctor) {
            $mensaje .= "\nDr(a). {$doctor}.";
        }

        $mensaje .= "\nSi tienes dudas, estamos atentos.";

        return $mensaje;
    }
}
