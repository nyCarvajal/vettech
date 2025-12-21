<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispensationRequest;
use App\Models\Prescription;
use App\Services\DispenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DispensationsController extends Controller
{
    public function __construct(private DispenseService $dispenseService)
    {
    }

    public function index(): View
    {
        $pending = Prescription::whereIn('status', ['signed', 'partial'])->with('items')->get();
        return view('dispensation.dispensations.index', compact('pending'));
    }

    public function store(Prescription $prescription, DispensationRequest $request): RedirectResponse
    {
        $this->dispenseService->dispensePrescription($prescription, $request->validated()['items'], $request->user());
        return redirect()->route('dispensations.index')->with('status', 'Dispensación registrada');
    }
}
