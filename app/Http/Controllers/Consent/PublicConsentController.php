<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\PublicSignConsentRequest;
use App\Models\ConsentDocument;
use App\Models\ConsentPublicLink;
use App\Models\ConsentSignature;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicConsentController extends Controller
{
    public function show(string $token)
    {
        $link = $this->resolveLink($token);
        if (!$link) {
            abort(404);
        }

        $consent = $link->document()->with('signatures', 'template')->first();

        return view('consents.public-sign', [
            'consent' => $consent,
            'token' => $token,
        ]);
    }

    public function sign(PublicSignConsentRequest $request, string $token, SignatureService $signatureService)
    {
        $link = $this->resolveLink($token);
        if (!$link) {
            abort(404);
        }

        $consent = $link->document;
        $path = $signatureService->storeBase64($request->string('signature_base64'), $link->tenant_id);

        ConsentSignature::create([
            'tenant_id' => $link->tenant_id,
            'consent_document_id' => $consent->id,
            'signer_role' => 'owner',
            'signer_name' => $request->string('signer_name'),
            'signer_document' => $request->string('signer_document'),
            'signature_image_path' => $path,
            'signed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => 'public_link',
        ]);

        $link->increment('uses');
        $link->update(['last_used_at' => now()]);
        $consent->update(['status' => 'signed', 'signed_at' => now()]);

        return view('consents.public-signed');
    }

    private function resolveLink(string $token): ?ConsentPublicLink
    {
        $hash = ConsentPublicLink::hashToken($token);
        $link = ConsentPublicLink::where('token_hash', $hash)->first();
        if (!$link || !$link->isValid()) {
            return null;
        }

        return $link;
    }
}
