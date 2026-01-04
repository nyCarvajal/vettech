<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\StoreConsentDocumentRequest;
use App\Models\ConsentAttachment;
use App\Models\ConsentDocument;
use App\Models\ConsentTemplate;
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
        return view('consents.create', compact('templates'));
    }

    public function store(StoreConsentDocumentRequest $request, PlaceholderService $placeholderService, ConsentCodeGenerator $codeGenerator)
    {
        $template = ConsentTemplate::findOrFail($request->integer('template_id'));

        $owner = $request->input('owner_snapshot', []);
        $pet = $request->input('pet_snapshot', []);
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
            'owner_snapshot' => $owner,
            'pet_snapshot' => $pet,
            'merged_body_html' => $mergedHtml,
            'merged_plain_text' => strip_tags($mergedHtml),
            'created_by' => $request->user()?->id,
        ]);

        return redirect()->route('consents.show', $document)->with('status', 'Consentimiento listo para firmar');
    }

    public function show(ConsentDocument $consent)
    {
        $consent->load('template', 'signatures', 'publicLinks');
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
}
