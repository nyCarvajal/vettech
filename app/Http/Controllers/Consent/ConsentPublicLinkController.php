<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\CreateConsentPublicLinkRequest;
use App\Models\ConsentDocument;
use App\Models\ConsentPublicLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsentPublicLinkController extends Controller
{
    public function create(CreateConsentPublicLinkRequest $request, ConsentDocument $consent)
    {
        $this->authorize('createPublicLink', $consent);
        $token = ConsentPublicLink::generateToken();
        $hash = ConsentPublicLink::hashToken($token);

        $link = ConsentPublicLink::create([
            'tenant_id' => $request->user()->tenant_id ?? null,
            'consent_document_id' => $consent->id,
            'token_hash' => $hash,
            'expires_at' => $request->input('expires_at'),
            'max_uses' => $request->input('max_uses', 1),
            'created_by' => $request->user()?->id,
        ]);

        $url = url('/public/consents/sign/' . $token);

        return back()->with('public_link', $url);
    }

    public function revoke(Request $request, ConsentDocument $consent, ConsentPublicLink $link)
    {
        $this->authorize('createPublicLink', $consent);
        $link->update(['revoked_at' => now()]);

        return back()->with('status', 'Link revocado');
    }
}
