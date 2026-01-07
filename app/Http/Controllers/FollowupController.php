<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowupRequest;
use App\Http\Requests\UpdateFollowupRequest;
use App\Models\Encounter;
use App\Models\Followup;
use App\Models\Owner;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FollowupController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Followup::class);

        $query = Followup::with(['patient', 'owner'])
            ->orderByDesc('followup_at');

        if ($patientId = request('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        if ($status = request('improved_status')) {
            $query->where('improved_status', $status);
        }

        if ($from = request('from')) {
            $query->whereDate('followup_at', '>=', $from);
        }

        if ($to = request('to')) {
            $query->whereDate('followup_at', '<=', $to);
        }

        $followups = $query->paginate(15)->withQueryString();
        $patients = Patient::orderBy('nombres')->get();

        return view('followups.index', compact('followups', 'patients'));
    }

    public function create(): View
    {
        $this->authorize('create', Followup::class);

        $followup = new Followup([
            'followup_at' => now(),
            'performed_by' => Auth::user()?->name,
        ]);
        $patients = Patient::orderBy('nombres')->get();
        $owners = Owner::orderBy('name')->get();
        $consultation = request('consultation_id') ? Encounter::find(request('consultation_id')) : null;
        $patient = request('patient_id') ? Patient::find(request('patient_id')) : null;

        return view('followups.form', [
            'followup' => $followup,
            'patients' => $patients,
            'owners' => $owners,
            'consultation' => $consultation,
            'patient' => $patient,
            'mode' => 'create',
        ]);
    }

    public function store(StoreFollowupRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $tenantId = $data['tenant_id'] ?? (Auth::user()->tenant_id ?? null);

        $patient = isset($data['patient_id']) ? Patient::find($data['patient_id']) : null;
        $owner = isset($data['owner_id']) ? Owner::find($data['owner_id']) : null;

        $followup = Followup::create([
            'tenant_id' => $tenantId,
            'patient_id' => $patient?->id,
            'owner_id' => $owner?->id,
            'consultation_id' => $data['consultation_id'] ?? null,
            'patient_snapshot' => $patient?->toArray(),
            'owner_snapshot' => $owner?->toArray(),
            'followup_at' => $data['followup_at'],
            'performed_by' => $data['performed_by'] ?? null,
            'performed_by_license' => $data['performed_by_license'] ?? null,
            'reason' => $data['reason'] ?? null,
            'improved_status' => $data['improved_status'],
            'improved_score' => $data['improved_score'] ?? null,
            'observations' => $data['observations'] ?? null,
            'plan' => $data['plan'] ?? null,
            'next_followup_at' => $data['next_followup_at'] ?? null,
        ]);

        if (! empty($data['vitals'])) {
            $followup->vitals()->create($data['vitals']);
        }

        return redirect()->route('followups.show', $followup)->with('status', 'Control registrado correctamente');
    }

    public function show(Followup $followup): View
    {
        $this->authorize('view', $followup);

        $followup->load(['patient', 'owner', 'vitals', 'attachments']);

        return view('followups.show', compact('followup'));
    }

    public function edit(Followup $followup): View
    {
        $this->authorize('update', $followup);

        $followup->load(['patient', 'owner', 'vitals']);
        $patients = Patient::orderBy('nombres')->get();
        $owners = Owner::orderBy('name')->get();

        return view('followups.form', [
            'followup' => $followup,
            'patients' => $patients,
            'owners' => $owners,
            'consultation' => $followup->consultation,
            'patient' => $followup->patient,
            'mode' => 'edit',
        ]);
    }

    public function update(UpdateFollowupRequest $request, Followup $followup): RedirectResponse
    {
        $data = $request->validated();

        $tenantId = $data['tenant_id'] ?? (Auth::user()->tenant_id ?? null);

        $followup->update([
            'tenant_id' => $tenantId,
            'patient_id' => $data['patient_id'] ?? null,
            'owner_id' => $data['owner_id'] ?? null,
            'consultation_id' => $data['consultation_id'] ?? null,
            'followup_at' => $data['followup_at'],
            'performed_by' => $data['performed_by'] ?? null,
            'performed_by_license' => $data['performed_by_license'] ?? null,
            'reason' => $data['reason'] ?? null,
            'improved_status' => $data['improved_status'],
            'improved_score' => $data['improved_score'] ?? null,
            'observations' => $data['observations'] ?? null,
            'plan' => $data['plan'] ?? null,
            'next_followup_at' => $data['next_followup_at'] ?? null,
        ]);

        if (! empty($data['patient_id'])) {
            $followup->patient_snapshot = optional(Patient::find($data['patient_id']))?->toArray();
        }

        if (! empty($data['owner_id'])) {
            $followup->owner_snapshot = optional(Owner::find($data['owner_id']))?->toArray();
        }

        $followup->save();

        if (! empty($data['vitals'])) {
            $followup->vitals()->updateOrCreate([], $data['vitals']);
        }

        return redirect()->route('followups.show', $followup)->with('status', 'Control actualizado');
    }

    public function destroy(Followup $followup): RedirectResponse
    {
        $this->authorize('delete', $followup);

        $followup->attachments()->each(function ($attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
        });

        $followup->delete();

        return redirect()->route('followups.index')->with('status', 'Control eliminado');
    }
}
