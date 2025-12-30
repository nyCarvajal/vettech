<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientImmunizationRequest;
use App\Models\InventarioHistorial;
use App\Models\Item;
use App\Models\Patient;
use App\Models\PatientImmunization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientImmunizationController extends Controller
{
    public function index(Request $request, Patient $patient): View
    {
        $query = $patient->immunizations()->with(['item', 'vet'])->orderByDesc('applied_at');

        if ($request->filled('from')) {
            $query->whereDate('applied_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('applied_at', '<=', $request->date('to'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('vaccine_name', 'like', "%{$search}%")
                    ->orWhere('item_manual', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }

        return view('patients.immunizations.index', [
            'patient' => $patient,
            'immunizations' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Patient $patient): View
    {
        return view('patients.immunizations.form', [
            'patient' => $patient,
            'immunization' => new PatientImmunization([
                'applied_at' => now()->toDateString(),
                'status' => 'applied',
            ]),
            'items' => Item::orderBy('nombre')->get(),
        ]);
    }

    public function store(PatientImmunizationRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();
        $data['paciente_id'] = $patient->id;
        $data['item_manual'] = $data['item_id'] ? null : $data['item_manual'];

        DB::transaction(function () use ($data) {
            $record = PatientImmunization::create($data);

            if ($record->item_id) {
                $this->consumeStock($record->item_id, 'Vacunación #' . $record->id);
            }
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Vacuna registrada correctamente.');
    }

    public function edit(Patient $patient, PatientImmunization $immunization): View
    {
        abort_unless($immunization->paciente_id === $patient->id, 404);

        return view('patients.immunizations.form', [
            'patient' => $patient,
            'immunization' => $immunization,
            'items' => Item::orderBy('nombre')->get(),
        ]);
    }

    public function update(PatientImmunizationRequest $request, Patient $patient, PatientImmunization $immunization): RedirectResponse
    {
        abort_unless($immunization->paciente_id === $patient->id, 404);

        $data = $request->validated();
        $data['paciente_id'] = $patient->id;
        $data['item_manual'] = $data['item_id'] ? null : $data['item_manual'];

        DB::transaction(function () use ($data, $immunization) {
            $previousItem = $immunization->item_id;

            if ($previousItem && $previousItem !== ($data['item_id'] ?? null)) {
                $this->restoreStock($immunization->item_id, 'Edición vacunación #' . $immunization->id);
            }

            $immunization->update($data);

            if ($immunization->item_id && ($previousItem !== $immunization->item_id)) {
                $this->consumeStock($immunization->item_id, 'Vacunación #' . $immunization->id);
            }
        });

        return redirect()
            ->route('patients.show', ['patient' => $patient, 'tab' => 'carnet'])
            ->with('status', 'Vacuna actualizada.');
    }

    public function destroy(Patient $patient, PatientImmunization $immunization): RedirectResponse
    {
        abort_unless($immunization->paciente_id === $patient->id, 404);

        DB::transaction(function () use ($immunization) {
            if ($immunization->item_id) {
                $this->restoreStock($immunization->item_id, 'Eliminación vacunación #' . $immunization->id);
            }
            $immunization->delete();
        });

        return back()->with('status', 'Vacuna eliminada.');
    }

    protected function consumeStock(int $itemId, string $reason): void
    {
        $item = Item::findOrFail($itemId);

        if ($item->tipo == 1 && $item->cantidad <= 0) {
            abort(422, 'Stock insuficiente para el item seleccionado.');
        }

        if ($item->tipo == 1) {
            $item->decrement('cantidad', 1);
            InventarioHistorial::create([
                'item_id' => $item->id,
                'cambio' => -1,
                'descripcion' => $reason,
            ]);
        }
    }

    protected function restoreStock(int $itemId, string $reason): void
    {
        $item = Item::find($itemId);

        if ($item && $item->tipo == 1) {
            $item->increment('cantidad', 1);
            InventarioHistorial::create([
                'item_id' => $item->id,
                'cambio' => 1,
                'descripcion' => $reason,
            ]);
        }
    }
}
