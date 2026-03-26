<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\PublicSignConsentRequest;
use App\Models\Clinica;
use App\Models\ConsentPublicLink;
use App\Models\ConsentSignature;
use App\Services\SignatureService;
use App\Support\TenantDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (! $this->connectTenantForPublicToken($token, $hash)) {
            return null;
        }

        $link = ConsentPublicLink::where('token_hash', $hash)->first();
        if (!$link || !$link->isValid()) {
            return null;
        }

        return $link;
    }

    private function connectTenantForPublicToken(string $token, string $hash): bool
    {
        if (config('database.connections.tenant.database')) {
            return true;
        }

        $tokenTenantId = $this->extractTenantIdFromToken($token);
        if ($tokenTenantId && $this->connectByTenantId($tokenTenantId)) {
            return true;
        }

        if (Schema::connection('mysql')->hasTable('consent_public_links')) {
            $tenantId = DB::connection('mysql')
                ->table('consent_public_links')
                ->where('token_hash', $hash)
                ->value('tenant_id');

            if ($tenantId && $this->connectByTenantId((int) $tenantId)) {
                return true;
            }
        }

        return $this->connectBySearchingHashAcrossClinics($hash);
    }

    private function extractTenantIdFromToken(string $token): ?int
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        return ctype_digit($parts[0]) ? (int) $parts[0] : null;
    }

    private function connectByTenantId(int $tenantId): bool
    {
        $clinica = Clinica::on('mysql')->find($tenantId);
        $database = $clinica?->db;

        if (! $database) {
            return false;
        }

        TenantDatabase::connect($database);
        return true;
    }

    private function connectBySearchingHashAcrossClinics(string $hash): bool
    {
        $clinicas = Clinica::on('mysql')
            ->whereNotNull('db')
            ->select(['id', 'db'])
            ->get();

        foreach ($clinicas as $clinica) {
            TenantDatabase::connect($clinica->db);

            if (ConsentPublicLink::where('token_hash', $hash)->exists()) {
                return true;
            }
        }

        return false;
    }
}
