<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProcedureConsentRequest;
use App\Http\Requests\LinkSignedConsentRequest;
use App\Models\ConsentDocument;
use App\Models\Procedure;
use App\Models\ProcedureEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProcedureConsentController extends Controller
{
    public function linkSignedConsent(LinkSignedConsentRequest $request, Procedure $procedure): RedirectResponse
    {
        $consent = ConsentDocument::where('id', $request->integer('consent_document_id'))
            ->where('status', 'signed')
            ->firstOrFail();

        $procedure->update(['consent_document_id' => $consent->id]);

        ProcedureEvent::create([
            'procedure_id' => $procedure->id,
            'event_type' => 'consent_linked',
            'payload' => ['consent_document_id' => $consent->id],
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', 'Consentimiento vinculado');
    }

    public function createFromTemplate(CreateProcedureConsentRequest $request, Procedure $procedure): RedirectResponse
    {
        $consent = ConsentDocument::create([
            'patient_id' => $procedure->patient_id,
            'owner_id' => $procedure->owner_id,
            'patient_snapshot' => $procedure->patient_snapshot,
            'owner_snapshot' => $procedure->owner_snapshot,
            'template_id' => $request->integer('template_id'),
            'status' => 'pending',
        ]);

        $procedure->update(['consent_document_id' => $consent->id]);

        ProcedureEvent::create([
            'procedure_id' => $procedure->id,
            'event_type' => 'consent_created',
            'payload' => ['consent_document_id' => $consent->id],
            'created_by' => Auth::id(),
        ]);

        return back()->with('status', 'Consentimiento generado desde plantilla');
    }
}
