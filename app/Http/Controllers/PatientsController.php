<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Breed;
use App\Models\Encounter;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Species;
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
        $breeds = Breed::when($request->filled('species_id'), function ($query) use ($request) {
            $query->where('species_id', $request->integer('species_id'));
        })->orderBy('name')->get();

        return view('patients.form', [
            'patient' => new Patient(),
            'species' => $species,
            'owners' => $owners,
            'breeds' => $breeds,
        ]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients', 'public');
        }

        $patient = Patient::create($data);

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
        $breeds = Breed::when($request->filled('species_id'), function ($query) use ($request) {
            $query->where('species_id', $request->integer('species_id'));
        }, function ($query) use ($patient) {
            if ($patient->species_id) {
                $query->where('species_id', $patient->species_id);
            }
        })->orderBy('name')->get();

        return view('patients.form', compact('patient', 'species', 'owners', 'breeds'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patients', 'public');
        }

        $patient->update($data);

        return redirect()->route('patients.show', $patient)->with('status', 'Paciente actualizado');
    }
}
