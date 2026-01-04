<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\StoreConsentSignatureRequest;
use App\Models\ConsentDocument;
use App\Models\ConsentSignature;
use App\Services\SignatureService;
use Illuminate\Http\Request;

class ConsentSignatureController extends Controller
{
    public function store(StoreConsentSignatureRequest $request, ConsentDocument $consent, SignatureService $signatureService)
    {
        $this->authorize('sign', $consent);
        $path = $signatureService->storeBase64($request->string('signature_base64'), $request->user()->tenant_id ?? null);

        ConsentSignature::create([
            'tenant_id' => $request->user()->tenant_id ?? null,
            'consent_document_id' => $consent->id,
            'signer_role' => $request->string('signer_role'),
            'signer_name' => $request->string('signer_name'),
            'signer_document' => $request->string('signer_document'),
            'signature_image_path' => $path,
            'signed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => 'internal',
            'geo_hint' => $request->string('geo_hint'),
        ]);

        $consent->update(['status' => 'signed', 'signed_at' => now()]);

        return redirect()->route('consents.show', $consent)->with('status', 'Firma guardada');
    }
}
