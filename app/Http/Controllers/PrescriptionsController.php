<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrescriptionRequest;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PrescriptionsController extends Controller
{
    public function index(): View
    {
        $prescriptions = Prescription::with('items')->latest()->paginate(15);
        return view('dispensation.prescriptions.index', compact('prescriptions'));
    }

    public function create(): View
    {
        return view('dispensation.prescriptions.create');
    }

    public function store(PrescriptionRequest $request): RedirectResponse
    {
        $prescription = Prescription::create($request->only(['encounter_id', 'patient_id', 'professional_id', 'status', 'observations']));
        foreach ($request->input('items') as $item) {
            PrescriptionItem::create($item + ['prescription_id' => $prescription->id]);
        }

        return redirect()->route('prescriptions.index')->with('status', 'Fórmula creada');
    }
}
