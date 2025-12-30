<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientVaccineCardController extends Controller
{
    public function show(Request $request, Patient $patient): View
    {
        $patient->load(['owner', 'species', 'breed', 'immunizations.item', 'dewormings.item']);

        $filters = [
            'from' => $request->date('from'),
            'to' => $request->date('to'),
            'q' => $request->string('q')->toString(),
        ];

        $immunizations = $patient->immunizations()->when($filters['from'], fn ($q) => $q->whereDate('applied_at', '>=', $filters['from']))
            ->when($filters['to'], fn ($q) => $q->whereDate('applied_at', '<=', $filters['to']))
            ->when($filters['q'], function ($q) use ($filters) {
                $q->where(function ($builder) use ($filters) {
                    $builder->where('vaccine_name', 'like', "%{$filters['q']}%")
                        ->orWhere('item_manual', 'like', "%{$filters['q']}%")
                        ->orWhereHas('item', fn ($i) => $i->where('nombre', 'like', "%{$filters['q']}%"));
                });
            })
            ->orderByDesc('applied_at')
            ->get();

        $dewormingsQuery = $patient->dewormings()->when($filters['from'], fn ($q) => $q->whereDate('applied_at', '>=', $filters['from']))
            ->when($filters['to'], fn ($q) => $q->whereDate('applied_at', '<=', $filters['to']))
            ->when($filters['q'], function ($q) use ($filters) {
                $q->where(function ($builder) use ($filters) {
                    $builder->where('item_manual', 'like', "%{$filters['q']}%")
                        ->orWhereHas('item', fn ($i) => $i->where('nombre', 'like', "%{$filters['q']}%"));
                });
            });

        return view('patients.carnet', [
            'patient' => $patient,
            'immunizations' => $immunizations,
            'internalDewormings' => (clone $dewormingsQuery)->where('type', 'internal')->orderByDesc('applied_at')->get(),
            'externalDewormings' => (clone $dewormingsQuery)->where('type', 'external')->orderByDesc('applied_at')->get(),
            'filters' => $filters,
        ]);
    }

    public function pdf(Patient $patient)
    {
        $patient->load(['owner', 'species', 'breed', 'immunizations.item', 'dewormings.item']);

        $data = [
            'patient' => $patient,
            'immunizations' => $patient->immunizations()->orderByDesc('applied_at')->get(),
            'internalDewormings' => $patient->dewormings()->where('type', 'internal')->orderByDesc('applied_at')->get(),
            'externalDewormings' => $patient->dewormings()->where('type', 'external')->orderByDesc('applied_at')->get(),
        ];

        $pdf = Pdf::loadView('patients.carnet-pdf', $data);

        return $pdf->download('carnet-' . $patient->id . '.pdf');
    }
}
