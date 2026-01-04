<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelTravelCertificateRequest;
use App\Http\Requests\IssueTravelCertificateRequest;
use App\Http\Requests\StoreTravelCertificateRequest;
use App\Http\Requests\UpdateTravelCertificateRequest;
use App\Models\Country;
use App\Models\GeoDepartment;
use App\Models\GeoMunicipality;
use App\Models\Patient;
use App\Models\TravelCertificate;
use App\Models\TravelCertificateAttachment;
use App\Models\TravelCertificateDeworming;
use App\Models\TravelCertificateVaccination;
use App\Models\User;
use App\Models\Clinica;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TravelCertificateController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TravelCertificate::class);

        $query = TravelCertificate::query();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('travel_departure_from')) {
            $query->whereDate('travel_departure_date', '>=', $request->date('travel_departure_from'));
        }
        if ($request->filled('travel_departure_to')) {
            $query->whereDate('travel_departure_date', '<=', $request->date('travel_departure_to'));
        }
        if ($request->filled('owner_name')) {
            $query->where('owner_name', 'like', '%' . $request->owner_name . '%');
        }
        if ($request->filled('pet_name')) {
            $query->where('pet_name', 'like', '%' . $request->pet_name . '%');
        }

        /** @var LengthAwarePaginator $certificates */
        $certificates = $query->latest()->paginate(15)->withQueryString();

        return view('travel_certificates.index', compact('certificates'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', TravelCertificate::class);

        $departments = GeoDepartment::orderBy('nombre')->get();
        $countries = Country::orderBy('name_es')->get();
        $user = $request->user();
        $clinic = Clinica::resolveForUser($user) ?? optional($user)->clinica;
        $defaultClinic = config('travel_certificates.default_clinic');
        if ($clinic) {
            $defaultClinic['name'] = $clinic->nombre ?? $defaultClinic['name'];
            $defaultClinic['nit'] = $clinic->nit ?? $defaultClinic['nit'];
            $defaultClinic['address'] = $clinic->direccion ?? $defaultClinic['address'];
            $defaultClinic['phone'] = $clinic->telefono ?? $defaultClinic['phone'];
            $defaultClinic['city'] = $clinic->municipio ?? $defaultClinic['city'];
        }
        if ($user) {
            $defaultClinic['vet_name'] = trim(($user->nombres ?? '')) ?: $defaultClinic['vet_name'];
            $defaultClinic['vet_license'] = $user->firma_medica_texto ?? $user->numero_identificacion ?? $defaultClinic['vet_license'];
        }
        $vets = User::query()
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'numero_identificacion', 'firma_medica_texto']);

        if ($user) {
            $vets = $vets
                ->sortBy(fn ($vet) => $vet->id === $user->id ? 0 : 1)
                ->values();
        }
        $declaration = config('travel_certificates.default_declaration');
        $patient = $request->filled('patient_id')
            ? Patient::with(['owner.departamento', 'owner.municipio', 'species', 'breed'])->find($request->integer('patient_id'))
            : null;
        $prefill = $patient ? $this->prefillFromPatient($patient) : [];
        $certificate = new TravelCertificate();

        return view('travel_certificates.create', compact('departments', 'countries', 'defaultClinic', 'declaration', 'patient', 'prefill', 'certificate', 'vets', 'user'));
    }

    public function store(StoreTravelCertificateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = TravelCertificate::generateCode($request->user()->tenant_id ?? null);
        $data['origin_type'] = $data['type'] === 'national_co' ? 'co' : 'international';
        $data['status'] = 'draft';

        $certificateAttributes = $this->extractCertificateAttributes($data, $request);

        $certificate = TravelCertificate::create($certificateAttributes);

        $this->syncRelations($certificate, $data, $request);

        $redirect = $request->boolean('save_and_continue')
            ? redirect()->route('travel-certificates.edit', $certificate)
            : redirect()->route('travel-certificates.show', $certificate);

        return $redirect->with('status', 'Certificado creado');
    }

    public function show(TravelCertificate $travel_certificate): View
    {
        Gate::authorize('view', $travel_certificate);

        return view('travel_certificates.show', ['certificate' => $travel_certificate->load(['vaccinations', 'dewormings', 'attachments'])]);
    }

    public function edit(TravelCertificate $travel_certificate): View
    {
        Gate::authorize('update', $travel_certificate);

        $departments = GeoDepartment::orderBy('nombre')->get();
        $countries = Country::orderBy('name_es')->get();
        $declaration = $travel_certificate->declaration_text;
        $user = request()->user();
        $clinic = Clinica::resolveForUser($user) ?? optional($user)->clinica;
        $defaultClinic = config('travel_certificates.default_clinic');
        if ($clinic) {
            $defaultClinic['name'] = $clinic->nombre ?? $defaultClinic['name'];
            $defaultClinic['nit'] = $clinic->nit ?? $defaultClinic['nit'];
            $defaultClinic['address'] = $clinic->direccion ?? $defaultClinic['address'];
            $defaultClinic['phone'] = $clinic->telefono ?? $defaultClinic['phone'];
            $defaultClinic['city'] = $clinic->municipio ?? $defaultClinic['city'];
        }
        if ($user) {
            $defaultClinic['vet_name'] = trim(($user->nombre ?? '') . ' ' . ($user->apellidos ?? '')) ?: $defaultClinic['vet_name'];
            $defaultClinic['vet_license'] = $user->firma_medica_texto ?? $user->numero_identificacion ?? $defaultClinic['vet_license'];
        }
        $vets = User::query()
            ->when($clinic, fn ($query) => $query->where('clinica_id', $clinic->id))
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellidos', 'numero_identificacion', 'firma_medica_texto']);

        return view('travel_certificates.edit', [
            'certificate' => $travel_certificate->load(['vaccinations', 'dewormings', 'attachments']),
            'departments' => $departments,
            'countries' => $countries,
            'declaration' => $declaration,
            'defaultClinic' => $defaultClinic,
            'vets' => $vets,
            'user' => $user,
        ]);
    }

    public function update(UpdateTravelCertificateRequest $request, TravelCertificate $travel_certificate): RedirectResponse
    {
        $data = $request->validated();
        $data['origin_type'] = $data['type'] === 'national_co' ? 'co' : 'international';

        $certificateAttributes = $this->extractCertificateAttributes($data, $request, $travel_certificate);

        $travel_certificate->update($certificateAttributes);
        $this->syncRelations($travel_certificate, $data, $request);

        return redirect()->route('travel-certificates.show', $travel_certificate)->with('status', 'Certificado actualizado');
    }

    public function destroy(TravelCertificate $travel_certificate): RedirectResponse
    {
        Gate::authorize('delete', $travel_certificate);
        $travel_certificate->delete();

        return redirect()->route('travel-certificates.index')->with('status', 'Certificado eliminado');
    }

    public function issue(IssueTravelCertificateRequest $request, TravelCertificate $travel_certificate): RedirectResponse
    {
        $issuedAt = $request->date('issued_at') ?? now();
        $expiresAt = $request->date('expires_at') ?? Carbon::parse($issuedAt)->addDays(config('travel_certificates.default_validity_days', 5));

        $travel_certificate->update([
            'status' => 'issued',
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
        ]);

        return redirect()->route('travel-certificates.show', $travel_certificate)->with('status', 'Certificado emitido');
    }

    public function cancel(CancelTravelCertificateRequest $request, TravelCertificate $travel_certificate): RedirectResponse
    {
        $travel_certificate->update([
            'status' => 'canceled',
            'canceled_reason' => $request->string('canceled_reason'),
        ]);

        return redirect()->route('travel-certificates.show', $travel_certificate)->with('status', 'Certificado anulado');
    }

    public function duplicate(TravelCertificate $travel_certificate): RedirectResponse
    {
        Gate::authorize('duplicate', $travel_certificate);

        $clone = $travel_certificate->replicate(['code', 'status', 'issued_at', 'expires_at']);
        $clone->status = 'draft';
        $clone->code = TravelCertificate::generateCode($travel_certificate->tenant_id);
        $clone->save();

        foreach ($travel_certificate->vaccinations as $vaccination) {
            $clone->vaccinations()->create(Arr::except($vaccination->toArray(), ['id', 'travel_certificate_id', 'created_at', 'updated_at']));
        }

        foreach ($travel_certificate->dewormings as $deworming) {
            $clone->dewormings()->create(Arr::except($deworming->toArray(), ['id', 'travel_certificate_id', 'created_at', 'updated_at']));
        }

        return redirect()->route('travel-certificates.edit', $clone)->with('status', 'Certificado duplicado');
    }

    public function pdf(TravelCertificate $travel_certificate)
    {
        Gate::authorize('exportPdf', $travel_certificate);

        $pdf = Pdf::loadView('travel_certificates.pdf', ['certificate' => $travel_certificate->load(['vaccinations', 'dewormings', 'attachments'])]);
        return $pdf->download($travel_certificate->code . '.pdf');
    }

    protected function extractCertificateAttributes(array $data, Request $request, ?TravelCertificate $existing = null): array
    {
        $model = $existing ?? new TravelCertificate();

        $attributes = Arr::only($data, $model->getFillable());

        if (! $existing) {
            $attributes['tenant_id'] = $request->user()->tenant_id ?? null;
        }

        return $attributes;
    }

    protected function syncRelations(TravelCertificate $certificate, array $data, Request $request): void
    {
        $certificate->vaccinations()->delete();
        foreach ($data['vaccinations'] ?? [] as $vaccination) {
            $certificate->vaccinations()->create($vaccination);
        }

        $certificate->dewormings()->delete();
        foreach ($data['dewormings'] ?? [] as $deworming) {
            $certificate->dewormings()->create($deworming);
        }

        if ($request->has('attachments')) {
            $certificate->attachments()->delete();
            foreach ($request->attachments as $attachment) {
                $file = $attachment['file'];
                $path = $file->store('travel-certificates', ['disk' => 'public']);
                $certificate->attachments()->create([
                    'title' => $attachment['title'],
                    'file_path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'size_bytes' => $file->getSize(),
                ]);
            }
        }
    }

    protected function prefillFromPatient(Patient $patient): array
    {
        $weightKg = null;
        if ($patient->peso_actual !== null) {
            $weightKg = $patient->weight_unit === 'g'
                ? (float) $patient->peso_actual / 1000
                : (float) $patient->peso_actual;
        }

        $ageMonths = null;
        if ($patient->age_value !== null) {
            $ageMonths = $patient->age_unit === 'months'
                ? $patient->age_value
                : $patient->age_value * 12;
        }

        return [
            'pet_id' => $patient->id,
            'pet_name' => $patient->display_name,
            'pet_species' => optional($patient->species)->name,
            'pet_breed' => optional($patient->breed)->name,
            'pet_sex' => $patient->sexo,
            'pet_age_months' => $ageMonths,
            'pet_weight_kg' => $weightKg,
            'pet_color' => $patient->color,
            'pet_microchip' => $patient->microchip,
            'owner_name' => optional($patient->owner)->name,
            'owner_document_type' => optional($patient->owner)->document_type,
            'owner_document_number' => optional($patient->owner)->document,
            'owner_phone' => optional($patient->owner)->phone ?? optional($patient->owner)->whatsapp,
            'owner_email' => optional($patient->owner)->email,
            'owner_address' => optional($patient->owner)->address,
            'owner_city' => optional(optional($patient->owner)->municipio)->nombre
                ?? optional(optional($patient->owner)->departamento)->nombre
                ?? $patient->ciudad,
        ];
    }
}
