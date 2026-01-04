<?php

namespace App\Http\Controllers\Consent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\StoreConsentTemplateRequest;
use App\Http\Requests\Consent\UpdateConsentTemplateRequest;
use App\Models\ConsentTemplate;
use App\Services\HtmlSanitizer;
use App\Services\PlaceholderService;
use Illuminate\Http\Request;

class ConsentTemplateController extends Controller
{
    public function index()
    {
        $templates = ConsentTemplate::latest()->paginate();
        return view('consents.templates.index', compact('templates'));
    }

    public function create(PlaceholderService $placeholders)
    {
        return view('consents.templates.create', [
            'placeholders' => $placeholders->availablePlaceholders(),
            'template' => new ConsentTemplate(),
        ]);
    }

    public function store(StoreConsentTemplateRequest $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validated();
        $data['body_html'] = $sanitizer->sanitize($data['body_html']);
        $data['created_by'] = $request->user()?->id;
        ConsentTemplate::create($data);

        return redirect()->route('consent-templates.index')->with('status', 'Plantilla creada');
    }

    public function show(ConsentTemplate $consentTemplate)
    {
        return view('consents.templates.show', compact('consentTemplate'));
    }

    public function edit(ConsentTemplate $consentTemplate, PlaceholderService $placeholders)
    {
        return view('consents.templates.edit', [
            'template' => $consentTemplate,
            'placeholders' => $placeholders->availablePlaceholders(),
        ]);
    }

    public function update(UpdateConsentTemplateRequest $request, ConsentTemplate $consentTemplate, HtmlSanitizer $sanitizer)
    {
        $data = $request->validated();
        $data['body_html'] = $sanitizer->sanitize($data['body_html']);
        $consentTemplate->update($data);

        return redirect()->route('consent-templates.index')->with('status', 'Plantilla actualizada');
    }

    public function destroy(ConsentTemplate $consentTemplate)
    {
        $consentTemplate->delete();
        return redirect()->route('consent-templates.index')->with('status', 'Plantilla eliminada');
    }
}
