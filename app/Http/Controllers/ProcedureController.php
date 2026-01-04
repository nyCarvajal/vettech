<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeProcedureStatusRequest;
use App\Http\Requests\StoreProcedureRequest;
use App\Http\Requests\UpdateProcedureRequest;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\ProcedureEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProcedureController extends Controller
{
    public function index(Request $request): View
    {
        $query = Procedure::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('responsible_vet_name')) {
            $query->where('responsible_vet_name', 'like', '%' . $request->string('responsible_vet_name') . '%');
        }

        if ($request->filled('from')) {
            $query->whereDate('scheduled_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('scheduled_at', '<=', $request->date('to'));
        }

        $procedures = $query->latest()->paginate(15)->withQueryString();

        return view('procedures.index', compact('procedures'));
    }

    public function create(Request $request): View
    {
        $patient = $request->filled('patient_id')
            ? Patient::with(['owner', 'species', 'breed'])->find($request->integer('patient_id'))
            : null;

        return view('procedures.create', [
            'procedure' => new Procedure(),
            'patient' => $patient,
            'patientSnapshot' => $patient ? $this->buildPatientSnapshot($patient) : [],
            'ownerSnapshot' => $patient && $patient->owner ? $this->buildOwnerSnapshot($patient->owner) : [],
            'responsibleUsers' => $this->clinicUsers($request->user()),
            'defaultResponsibleName' => $this->responsibleDisplayName($request->user()),
            'defaultResponsibleLicense' => $this->resolveUserLicense($request->user()),
        ]);
    }

    public function store(StoreProcedureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['tenant_id'] = $request->user()->tenant_id ?? null;

        $patient = $request->filled('patient_id')
            ? Patient::with(['owner', 'species', 'breed'])->find($request->integer('patient_id'))
            : null;
        $owner = $patient?->owner;

        if ($patient) {
            $data['patient_id'] = $patient->id;
            $data['owner_id'] = $owner?->id;
            $data['patient_snapshot'] = $this->buildPatientSnapshot($patient);
            $data['owner_snapshot'] = $owner ? $this->buildOwnerSnapshot($owner) : [];
        } else {
            $data['patient_snapshot'] = is_string($data['patient_snapshot']) ? json_decode($data['patient_snapshot'], true) : $data['patient_snapshot'];
            $data['owner_snapshot'] = isset($data['owner_snapshot']) && is_string($data['owner_snapshot']) ? json_decode($data['owner_snapshot'], true) : $data['owner_snapshot'];
        }

        if (blank($data['responsible_vet_name'] ?? null)) {
            $data['responsible_vet_name'] = $this->responsibleDisplayName($request->user());
        }

        if (blank($data['responsible_vet_license'] ?? null)) {
            $data['responsible_vet_license'] = $this->resolveUserLicense($request->user());
        }

        $procedure = Procedure::create($data);

        if (! empty($data['anesthesia_medications'])) {
            $procedure->anesthesiaMedications()->createMany($data['anesthesia_medications']);
        }

        $this->logEvent($procedure, 'created');

        return redirect()->route('procedures.show', $procedure)->with('status', 'Procedimiento creado');
    }

    public function show(Procedure $procedure): View
    {
        return view('procedures.show', [
            'procedure' => $procedure->load('attachments', 'anesthesiaMedications', 'consentDocument'),
        ]);
    }

    public function edit(Request $request, Procedure $procedure): View
    {
        $patient = $request->filled('patient_id')
            ? Patient::with(['owner', 'species', 'breed'])->find($request->integer('patient_id'))
            : ($procedure->patient_id ? Patient::with(['owner', 'species', 'breed'])->find($procedure->patient_id) : null);

        $patientSnapshot = $procedure->patient_snapshot ?: ($patient ? $this->buildPatientSnapshot($patient) : []);
        $ownerSnapshot = $procedure->owner_snapshot ?: ($patient && $patient->owner ? $this->buildOwnerSnapshot($patient->owner) : []);

        return view('procedures.edit', [
            'procedure' => $procedure->load('anesthesiaMedications'),
            'patient' => $patient,
            'patientSnapshot' => $patientSnapshot,
            'ownerSnapshot' => $ownerSnapshot,
            'responsibleUsers' => $this->clinicUsers($request->user()),
            'defaultResponsibleName' => $this->responsibleDisplayName($request->user()),
            'defaultResponsibleLicense' => $this->resolveUserLicense($request->user()),
        ]);
    }

    public function update(UpdateProcedureRequest $request, Procedure $procedure): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = Auth::id();

        $patient = $request->filled('patient_id')
            ? Patient::with(['owner', 'species', 'breed'])->find($request->integer('patient_id'))
            : ($procedure->patient_id ? Patient::with(['owner', 'species', 'breed'])->find($procedure->patient_id) : null);
        $owner = $patient?->owner;

        if ($patient) {
            $data['patient_id'] = $patient->id;
            $data['owner_id'] = $owner?->id;
            $data['patient_snapshot'] = $this->buildPatientSnapshot($patient);
            $data['owner_snapshot'] = $owner ? $this->buildOwnerSnapshot($owner) : [];
        } else {
            $data['patient_snapshot'] = is_string($data['patient_snapshot']) ? json_decode($data['patient_snapshot'], true) : $data['patient_snapshot'];
            $data['owner_snapshot'] = isset($data['owner_snapshot']) && is_string($data['owner_snapshot']) ? json_decode($data['owner_snapshot'], true) : $data['owner_snapshot'];
        }

        if (blank($data['responsible_vet_name'] ?? null)) {
            $data['responsible_vet_name'] = $this->responsibleDisplayName($request->user());
        }

        if (blank($data['responsible_vet_license'] ?? null)) {
            $data['responsible_vet_license'] = $this->resolveUserLicense($request->user());
        }

        $procedure->update($data);

        $procedure->anesthesiaMedications()->delete();
        if (! empty($data['anesthesia_medications'])) {
            $procedure->anesthesiaMedications()->createMany($data['anesthesia_medications']);
        }

        $this->logEvent($procedure, 'updated');

        return redirect()->route('procedures.show', $procedure)->with('status', 'Procedimiento actualizado');
    }

    public function destroy(Procedure $procedure): RedirectResponse
    {
        $procedure->delete();

        $this->logEvent($procedure, 'deleted');

        return redirect()->route('procedures.index')->with('status', 'Procedimiento eliminado');
    }

    public function changeStatus(ChangeProcedureStatusRequest $request, Procedure $procedure): RedirectResponse
    {
        $procedure->update($request->validated());
        $this->logEvent($procedure, 'status_changed', ['status' => $procedure->status]);

        return back()->with('status', 'Estado actualizado');
    }

    private function logEvent(Procedure $procedure, string $type, array $payload = []): void
    {
        ProcedureEvent::create([
            'procedure_id' => $procedure->id,
            'event_type' => $type,
            'payload' => $payload,
            'created_by' => Auth::id(),
        ]);
    }

    private function buildPatientSnapshot(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'name' => $patient->display_name,
            'species' => optional($patient->species)->name,
            'breed' => optional($patient->breed)->name,
            'sex' => $patient->sexo,
            'age' => $patient->edad,
            'weight' => $patient->peso_formateado,
            'color' => $patient->color,
            'microchip' => $patient->microchip,
        ];
    }

    private function buildOwnerSnapshot(Owner $owner): array
    {
        return [
            'id' => $owner->id,
            'name' => $owner->name,
            'phone' => $owner->phone ?? $owner->whatsapp,
            'email' => $owner->email,
            'document' => $owner->document,
            'address' => $owner->address,
        ];
    }

    private function clinicUsers(?User $user)
    {
        return User::query()
            ->when($user?->clinica_id, fn ($query, $clinicId) => $query->where('clinica_id', $clinicId))
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get(['id', 'nombre', 'apellidos', 'numero_identificacion', 'firma_medica_texto']);
    }

    private function responsibleDisplayName(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return trim(($user->nombre ?? '') . ' ' . ($user->apellidos ?? '')) ?: null;
    }

    private function resolveUserLicense(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        return $user->firma_medica_texto ?? $user->numero_identificacion;
    }
}
