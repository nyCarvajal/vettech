<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroomingRequest;
use App\Models\Grooming;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\Product;
use App\Services\GroomingService as GroomingWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroomingController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        $status = $request->input('status');

        $groomings = Grooming::with(['patient.owner', 'serviceProduct', 'groomingService'])
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy('status');

        $patients = Patient::with('owner')->orderBy('nombres')->get();

        return view('groomings.index', [
            'groomings' => $groomings,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'status' => $status,
            'patients' => $patients,
        ]);
    }

    public function create(Request $request): View
    {
        $patient = $request->filled('patient_id')
            ? Patient::with('owner')->find($request->input('patient_id'))
            : null;

        $owners = Owner::orderBy('name')->get();
        $patients = Patient::with('owner')->orderBy('nombres')->get();

        $serviceProducts = Product::where('type', 'servicio')
            ->where('inventariable', 0)
            ->orderBy('name')
            ->get();
        $inventoryProducts = Product::whereIn('type', ['med', 'insumo'])->orderBy('name')->get();

        return view('groomings.create', compact(
            'patient',
            'owners',
            'patients',
            'serviceProducts',
            'inventoryProducts'
        ));
    }

    public function store(GroomingRequest $request, GroomingWorkflowService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $grooming = $service->createGrooming($data);

        return redirect()
            ->route('groomings.show', $grooming)
            ->with('success', 'Peluquería registrada correctamente.');
    }

    public function show(Grooming $grooming): View
    {
        $grooming->load(['patient.owner', 'serviceProduct', 'groomingService', 'report']);

        return view('groomings.show', compact('grooming'));
    }
}
