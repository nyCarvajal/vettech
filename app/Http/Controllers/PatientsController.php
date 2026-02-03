<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Breed;
use App\Models\Encounter;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Species;
use App\Services\BreedService;
use App\Services\TimelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientsController extends Controller
{
    public function __construct(private TimelineService $timelineService)
    {
    }

    public function index(Request $request): View
    {
        $patients = Patient::query()
            ->with(['owner', 'species', 'breed'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('nombres', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('species_id'), fn ($q) => $q->where('species_id', $request->integer('species_id')))
            ->when($request->filled('breed_id'), fn ($q) => $q->where('breed_id', $request->integer('breed_id')))
            ->when($request->filled('owner_id'), fn ($q) => $q->where('owner_id', $request->integer('owner_id')))
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        $species = Species::orderBy('name')->get();
        $owners = Owner::orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();

        return view('patients.index', compact('patients', 'species', 'owners', 'breeds'));
    }

    public function create(Request $request): View
    {
        $species = Species::orderBy('name')->get();
        $owners = Owner::orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();

        $patient = new Patient([
            'owner_id' => $request->integer('owner_id') ?: null,
        ]);

        $patient->load('tutors');

        return view('patients.form', [
            'patient' => $patient,
            'species' => $species,
            'owners' => $owners,
            'breeds' => $breeds,
            'tutoresIniciales' => $this->buildTutorPayload($patient),
        ]);
    }

    public function store(PatientRequest $request, BreedService $breedService): RedirectResponse
    {
        $data = $request->validated();

        $data['breed_id'] = $this->resolveBreed($data, $breedService);
        unset($data['breed_name']);

        if (isset($data['peso_actual'])) {
            $data['weight_unit'] = $data['weight_unit'] ?? 'kg';
            $data['peso_actual'] = $data['weight_unit'] === 'g'
                ? $data['peso_actual'] / 1000
                : $data['peso_actual'];
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients', 'public');
        }

        $patient = Patient::create($data);

        $this->syncTutors($patient, $request);

        Encounter::create([
            'patient_id' => $patient->id,
            'occurred_at' => now(),
            'motivo' => 'Ingreso inicial',
            'diagnostico' => null,
            'plan' => null,
        ]);

        return redirect()->route('patients.show', $patient)->with('status', 'Paciente creado correctamente');
    }

    public function show(Request $request, Patient $patient): View
    {
        $patient->load(['owner', 'species', 'breed', 'lastEncounter', 'hospitalStays' => fn ($q) => $q->latest('admitted_at')]);
        $activeStay = $patient->hospitalStays->firstWhere('status', 'active');

        $filters = [
            'type' => $request->string('tipo')->toString(),
            'from' => $request->date('desde'),
            'to' => $request->date('hasta'),
        ];

        $timeline = $this->timelineService->forPatient($patient, $filters);

        return view('patients.show', [
            'patient' => $patient,
            'activeStay' => $activeStay,
            'timeline' => $timeline,
            'filters' => $filters,
        ]);
    }

    public function edit(Request $request, Patient $patient): View
    {
        $species = Species::orderBy('name')->get();
        $owners = Owner::orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();

        $patient->load('tutors');

        return view('patients.form', [
            'patient' => $patient,
            'species' => $species,
            'owners' => $owners,
            'breeds' => $breeds,
            'tutoresIniciales' => $this->buildTutorPayload($patient),
        ]);
    }

    public function update(PatientRequest $request, Patient $patient, BreedService $breedService): RedirectResponse
    {
        $data = $request->validated();

        $data['breed_id'] = $this->resolveBreed($data, $breedService);
        unset($data['breed_name']);

        if (isset($data['peso_actual'])) {
            $data['weight_unit'] = $data['weight_unit'] ?? 'kg';
            $data['peso_actual'] = $data['weight_unit'] === 'g'
                ? $data['peso_actual'] / 1000
                : $data['peso_actual'];
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients', 'public');
        }

        $patient->update($data);
        $this->syncTutors($patient, $request);

        return redirect()->route('patients.show', $patient)->with('status', 'Paciente actualizado');
    }

    private function resolveBreed(array $data, BreedService $breedService): ?int
    {
        if (! empty($data['breed_id'])) {
            return (int) $data['breed_id'];
        }

        $name = $data['breed_name'] ?? null;
        $speciesId = $data['species_id'] ?? null;

        if (! $name || ! $speciesId) {
            return null;
        }

        $breed = $breedService->findOrCreateFromName($name, (int) $speciesId);

        return $breed->id;
    }

    private function syncTutors(Patient $patient, Request $request): void
    {
        if (! $request->filled('tutores_json') && ! $request->filled('owner_id')) {
            return;
        }

        $payload = $this->decodeTutorPayload($request->input('tutores_json'));

        if (count($payload) === 0 && $request->filled('owner_id')) {
            $payload[] = [
                'id' => (int) $request->input('owner_id'),
                'es_principal' => true,
            ];
        }

        if (count($payload) === 0) {
            return;
        }

        $attachData = [];
        $primaryOwnerId = null;

        foreach ($payload as $item) {
            $owner = null;
            $ownerId = $item['id'] ?? null;

            if ($ownerId) {
                $owner = Owner::find($ownerId);
            } else {
                $name = trim(($item['nombres'] ?? '') . ' ' . ($item['apellidos'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $owner = Owner::create([
                    'name' => $name,
                    'phone' => $item['telefono'] ?? null,
                    'whatsapp' => $item['whatsapp'] ?? null,
                    'email' => $item['email'] ?? null,
                    'document' => $item['documento'] ?? null,
                    'document_type' => $item['tipo_documento'] ?? null,
                    'address' => $item['direccion'] ?? null,
                    'city' => $item['ciudad'] ?? null,
                    'notes' => $item['observaciones'] ?? null,
                ]);
            }

            if (! $owner) {
                continue;
            }

            $isPrimary = (bool) ($item['es_principal'] ?? false);
            if ($isPrimary && ! $primaryOwnerId) {
                $primaryOwnerId = $owner->id;
            }

            $attachData[$owner->id] = [
                'relationship' => $item['parentesco'] ?? null,
                'is_primary' => $isPrimary,
            ];
        }

        if (! $primaryOwnerId && count($attachData) > 0) {
            $primaryOwnerId = (int) array_key_first($attachData);
            $attachData[$primaryOwnerId]['is_primary'] = true;
        }

        $patient->tutors()->sync($attachData);

        if ($primaryOwnerId) {
            $patient->update(['owner_id' => $primaryOwnerId]);
        }
    }

    private function decodeTutorPayload(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function buildTutorPayload(Patient $patient): array
    {
        if (! $patient->relationLoaded('tutors')) {
            return [];
        }

        if ($patient->tutors->isEmpty() && $patient->owner) {
            $owner = $patient->owner;

            return [[
                'key' => 'owner-' . $owner->id,
                'id' => $owner->id,
                'nombre' => $owner->name,
                'documento' => $owner->document,
                'telefono' => $owner->phone,
                'whatsapp' => $owner->whatsapp,
                'email' => $owner->email,
                'parentesco' => null,
                'es_principal' => true,
                'is_new' => false,
            ]];
        }

        return $patient->tutors->map(function (Owner $owner) use ($patient) {
            $isPrimary = $patient->owner_id === $owner->id
                || (bool) ($owner->pivot->is_primary ?? false);

            return [
                'key' => 'owner-' . $owner->id,
                'id' => $owner->id,
                'nombre' => $owner->name,
                'documento' => $owner->document,
                'telefono' => $owner->phone,
                'whatsapp' => $owner->whatsapp,
                'email' => $owner->email,
                'parentesco' => $owner->pivot->relationship ?? null,
                'es_principal' => $isPrimary,
                'is_new' => false,
            ];
        })->values()->toArray();
    }
}
