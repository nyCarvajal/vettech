<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientDewormingRequest;
use App\Models\InventarioHistorial;
use App\Models\Item;
use App\Models\Patient;
use App\Models\PatientDeworming;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientDewormingController extends Controller
{
    public function index(Request $request, Patient $patient): View
    {
        $query = $patient->dewormings()->with(['item', 'vet'])->orderByDesc('applied_at');

        if ($request->filled('from')) {
            $query->whereDate('applied_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('applied_at', '<=', $request->date('to'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('item_manual', 'like', "%{$search}%")
                    ->orWhereHas('item', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        return view('patients.dewormings.index', [
            'patient' => $patient,
            'dewormings' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Patient $patient, string $type): View
    {
        return view('patients.dewormings.form', [
            'patient' => $patient,
            'type' => $type,
            'deworming' => new PatientDeworming([
                'applied_at' => now()->toDateString(),
                'status' => 'applied',
                'type' => $type,
            ]),
            'items' => Item::orderBy('nombre')->get(),
        ]);
    }

    public function store(PatientDewormingRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();
        $data['paciente_id'] = $patient->id;
        $data['item_manual'] = $data['item_id'] ? null : $data['item_manual'];

        if (! $data['next_due_at'] && $data['duration_days']) {
            $data['next_due_at'] = now()->addDays((int) $data['duration_days'])->toDateString();
        }

        DB::transaction(function () use ($data) {
            $record = PatientDeworming::create($data);

            if ($record->item_id) {
                $this->consumeStock($record->item_id, 'Desparasitación #' . $record->id);
            }
        });

        return redirect()->route('patients.show', ['patient' => $patient, 'tab' => 'carnet'])->with('status', 'Desparasitación guardada.');
    }

    public function edit(Patient $patient, PatientDeworming $deworming): View
    {
        abort_unless($deworming->paciente_id === $patient->id, 404);

        return view('patients.dewormings.form', [
            'patient' => $patient,
            'deworming' => $deworming,
            'type' => $deworming->type,
            'items' => Item::orderBy('nombre')->get(),
        ]);
    }

    public function update(PatientDewormingRequest $request, Patient $patient, PatientDeworming $deworming): RedirectResponse
    {
        abort_unless($deworming->paciente_id === $patient->id, 404);

        $data = $request->validated();
        $data['paciente_id'] = $patient->id;
        $data['item_manual'] = $data['item_id'] ? null : $data['item_manual'];

        if (! $data['next_due_at'] && $data['duration_days']) {
            $data['next_due_at'] = now()->addDays((int) $data['duration_days'])->toDateString();
        }

        DB::transaction(function () use ($data, $deworming) {
            $previousItem = $deworming->item_id;

            if ($previousItem && $previousItem !== ($data['item_id'] ?? null)) {
                $this->restoreStock($previousItem, 'Edición desparasitación #' . $deworming->id);
            }

            $deworming->update($data);

            if ($deworming->item_id && $previousItem !== $deworming->item_id) {
                $this->consumeStock($deworming->item_id, 'Desparasitación #' . $deworming->id);
            }
        });

        return redirect()->route('patients.show', ['patient' => $patient, 'tab' => 'carnet'])->with('status', 'Desparasitación actualizada.');
    }

    public function destroy(Patient $patient, PatientDeworming $deworming): RedirectResponse
    {
        abort_unless($deworming->paciente_id === $patient->id, 404);

        DB::transaction(function () use ($deworming) {
            if ($deworming->item_id) {
                $this->restoreStock($deworming->item_id, 'Eliminación desparasitación #' . $deworming->id);
            }
            $deworming->delete();
        });

        return back()->with('status', 'Registro eliminado.');
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
