<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\StoreConsentDocumentRequest;
use App\Models\ConsentAttachment;
use App\Models\ConsentDocument;
use App\Models\ConsentTemplate;
use App\Models\Owner;
use App\Models\Patient;
use App\Services\ConsentCodeGenerator;
use App\Services\PlaceholderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsentDocumentController extends Controller
{
    public function index()
    {
        $consents = ConsentDocument::with('template')->latest()->paginate();
        return view('consents.index', compact('consents'));
    }

    public function create(Request $request)
    {
        $templates = ConsentTemplate::where('is_active', true)->get();

        $patient = null;
        $ownerSnapshot = [];
        $petSnapshot = [];

        if ($request->filled('patient_id')) {
            $patient = Patient::with('owner', 'species', 'breed')->find($request->integer('patient_id'));

            if ($patient) {
                $ownerSnapshot = [
                    'full_name' => $patient->owner?->name,
                    'first_name' => $patient->owner?->name,
                    'phone' => $patient->owner?->phone,
                    'email' => $patient->owner?->email,
                    'document' => $patient->owner?->document,
                    'address' => $patient->owner?->address,
                    'city' => $patient->owner?->municipio?->nombre,
                ];

                $petSnapshot = [
                    'name' => $patient->display_name,
                    'species' => $patient->species?->name,
                    'breed' => $patient->breed?->name,
                    'sex' => $patient->sexo,
                    'age' => $patient->edad,
                    'weight' => $patient->peso_actual,
                    'color' => $patient->color,
                    'microchip' => $patient->microchip,
                ];
            }
        }

        return view('consents.create', compact('templates', 'patient', 'ownerSnapshot', 'petSnapshot'));
    }

    public function store(StoreConsentDocumentRequest $request, PlaceholderService $placeholderService, ConsentCodeGenerator $codeGenerator)
    {
        $template = ConsentTemplate::findOrFail($request->integer('template_id'));

        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::with('owner', 'species', 'breed')->find($request->integer('patient_id'));
        }

        $owner = $this->enrichOwnerSnapshot(
            $request->input('owner_snapshot', []),
            $patient?->owner
        );

        $pet = $this->enrichPetSnapshot(
            $request->input('pet_snapshot', []),
            $patient
        );

        $context = [
            'owner' => $owner,
            'pet' => $pet,
            'clinic' => $request->user()?->clinic ?? [],
            'vet' => ['name' => $request->user()?->name, 'license' => $request->user()?->license ?? null],
            'now' => ['date' => now()->toDateString(), 'datetime' => now()->toDateTimeString()],
        ];

        $mergedHtml = $placeholderService->merge($template->body_html, $context);

        $document = ConsentDocument::create([
            'tenant_id' => $request->user()->tenant_id ?? null,
            'code' => $codeGenerator->generate($request->user()->tenant_id ?? null),
            'status' => 'pending_signature',
            'template_id' => $template->id,
            'owner_id' => $patient?->owner?->id,
            'pet_id' => $patient?->id,
            'owner_snapshot' => $owner,
            'pet_snapshot' => $pet,
            'merged_body_html' => $mergedHtml,
            'merged_plain_text' => strip_tags($mergedHtml),
            'created_by' => $request->user()?->id,
        ]);

        return redirect()->route('consents.show', $document)->with('status', 'Consentimiento listo para firmar');
    }

    public function show(ConsentDocument $consent, PlaceholderService $placeholderService)
    {
        $consent->load(
            'template',
            'signatures',
            'publicLinks',
            'pet.owner',
            'pet.species',
            'pet.breed',
            'owner'
        );

        $originalOwnerSnapshot = $consent->owner_snapshot ?? [];
        $originalPetSnapshot = $consent->pet_snapshot ?? [];

        $ownerSnapshot = $this->enrichOwnerSnapshot(
            $originalOwnerSnapshot,
            $consent->owner ?? $consent->pet?->owner
        );

        $petSnapshot = $this->enrichPetSnapshot(
            $originalPetSnapshot,
            $consent->pet
        );

        // If the stored HTML is missing, still has placeholders, or we just enriched the snapshots, rebuild it
        $stillHasPlaceholders = empty($consent->merged_body_html)
            || preg_match('/\{\{\s*[a-zA-Z0-9_\.]+\s*\}\}/', $consent->merged_body_html);

        $snapshotsImproved = $ownerSnapshot !== $originalOwnerSnapshot
            || $petSnapshot !== $originalPetSnapshot;

        // Ensure the view and subsequent logic see the enriched data
        $consent->owner_snapshot = $ownerSnapshot;
        $consent->pet_snapshot = $petSnapshot;

        if (($stillHasPlaceholders || $snapshotsImproved) && $consent->template?->body_html) {
            $context = [
                'owner' => $ownerSnapshot,
                'pet' => $petSnapshot,
                'clinic' => auth()->user()?->clinic ?? [],
                'vet' => [
                    'name' => auth()->user()?->name,
                    'license' => auth()->user()?->license ?? null,
                ],
                'now' => ['date' => now()->toDateString(), 'datetime' => now()->toDateTimeString()],
            ];

            $mergedHtml = $placeholderService->merge($consent->template->body_html, $context);
            $consent->forceFill([
                'owner_snapshot' => $ownerSnapshot,
                'pet_snapshot' => $petSnapshot,
                'merged_body_html' => $mergedHtml,
                'merged_plain_text' => strip_tags($mergedHtml),
            ])->save();
        }

        return view('consents.show', compact('consent'));
    }

    public function cancel(Request $request, ConsentDocument $consent)
    {
        $this->authorize('cancel', $consent);
        $request->validate(['reason' => ['required', 'string']]);
        $consent->update(['status' => 'canceled', 'canceled_reason' => $request->string('reason')]);

        return back()->with('status', 'Consentimiento cancelado');
    }

    public function pdf(ConsentDocument $consent)
    {
        $this->authorize('pdf', $consent);
        $consent->load('signatures');
        $pdf = Pdf::loadView('consents.pdf', ['consent' => $consent]);
        $path = 'consents/pdfs/' . $consent->code . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        ConsentAttachment::firstOrCreate(
            ['consent_document_id' => $consent->id, 'title' => 'Consentimiento firmado'],
            ['file_path' => $path, 'mime' => 'application/pdf', 'size_bytes' => Storage::disk('public')->size($path)]
        );

        return $pdf->download($consent->code . '.pdf');
    }

    private function enrichOwnerSnapshot(array $snapshot, ?Owner $owner = null): array
    {
        if ($owner) {
            $snapshot['full_name'] = $snapshot['full_name'] ?? $owner->name;
            $snapshot['first_name'] = $snapshot['first_name'] ?? $owner->name;
            $snapshot['last_name'] = $snapshot['last_name'] ?? '';
            $snapshot['document'] = $snapshot['document'] ?? $owner->document;
            $snapshot['phone'] = $snapshot['phone'] ?? ($owner->phone ?? $owner->whatsapp ?? null);
            $snapshot['email'] = $snapshot['email'] ?? $owner->email;
            $snapshot['address'] = $snapshot['address'] ?? $owner->address;
            $snapshot['city'] = $snapshot['city'] ?? $owner->municipio?->nombre;
        }

        if (! isset($snapshot['full_name']) || $snapshot['full_name'] === '') {
            $maybeFull = trim(($snapshot['first_name'] ?? '') . ' ' . ($snapshot['last_name'] ?? ''));
            if ($maybeFull !== '') {
                $snapshot['full_name'] = $maybeFull;
            }
        }

        return $snapshot;
    }

    private function enrichPetSnapshot(array $snapshot, ?Patient $pet = null): array
    {
        if ($pet) {
            $snapshot['name'] = $snapshot['name'] ?? $pet->display_name;
            $snapshot['species'] = $snapshot['species'] ?? $pet->species?->name;
            $snapshot['breed'] = $snapshot['breed'] ?? $pet->breed?->name;
            $snapshot['sex'] = $snapshot['sex'] ?? $pet->sexo;
            $snapshot['age'] = $snapshot['age'] ?? $pet->edad;
            $snapshot['weight'] = $snapshot['weight'] ?? $pet->peso_actual;
            $snapshot['color'] = $snapshot['color'] ?? $pet->color;
            $snapshot['microchip'] = $snapshot['microchip'] ?? $pet->microchip;
        }

        return $snapshot;
    }
}
