<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeProcedureStatusRequest;
use App\Http\Requests\StoreProcedureRequest;
use App\Http\Requests\UpdateProcedureRequest;
use App\Models\Procedure;
use App\Models\ProcedureEvent;
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

    public function create(): View
    {
        return view('procedures.create', ['procedure' => new Procedure()]);
    }

    public function store(StoreProcedureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['tenant_id'] = $request->user()->tenant_id ?? null;
        $data['patient_snapshot'] = is_string($data['patient_snapshot']) ? json_decode($data['patient_snapshot'], true) : $data['patient_snapshot'];
        $data['owner_snapshot'] = isset($data['owner_snapshot']) && is_string($data['owner_snapshot']) ? json_decode($data['owner_snapshot'], true) : $data['owner_snapshot'];

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

    public function edit(Procedure $procedure): View
    {
        return view('procedures.edit', ['procedure' => $procedure->load('anesthesiaMedications')]);
    }

    public function update(UpdateProcedureRequest $request, Procedure $procedure): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = Auth::id();
        $data['patient_snapshot'] = is_string($data['patient_snapshot']) ? json_decode($data['patient_snapshot'], true) : $data['patient_snapshot'];
        $data['owner_snapshot'] = isset($data['owner_snapshot']) && is_string($data['owner_snapshot']) ? json_decode($data['owner_snapshot'], true) : $data['owner_snapshot'];

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
}
