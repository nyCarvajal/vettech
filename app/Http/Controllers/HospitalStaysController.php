<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConnectTenantDB;
use App\Http\Requests\HospitalStayRequest;
use App\Models\Cage;
use App\Models\HospitalStay;
use App\Models\Patient;
use App\Services\HospitalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HospitalStaysController extends Controller
{
    public function __construct(private HospitalService $hospitalService)
    {
        $this->middleware(ConnectTenantDB::class);
    }

    public function index(): View
    {
        $stays = HospitalStay::with('cage')->latest()->paginate(15);
        return view('hospital.stays.index', compact('stays'));
    }

    public function create(): View
    {
        $cages = Cage::where('active', true)->get();

        $patient = null;
        if ($patientId = request('patient_id')) {
            $patient = Patient::with(['owner', 'species'])->find($patientId);
        }

        return view('hospital.stays.create', compact('cages', 'patient'));
    }

    public function store(HospitalStayRequest $request): RedirectResponse
    {
        $this->hospitalService->admit($request->validated(), $request->user());
        return redirect()->route('hospital.stays.index')->with('status', 'Paciente hospitalizado');
    }

    public function discharge(HospitalStay $stay): RedirectResponse
    {
        $this->hospitalService->discharge($stay, request()->user());
        return redirect()->route('hospital.stays.index')->with('status', 'Alta registrada');
    }
}
