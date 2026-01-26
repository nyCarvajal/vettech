<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConnectTenantDB;
use App\Http\Requests\AdministrationRequest;
use App\Http\Requests\AdmitRequest;
use App\Http\Requests\ChargeRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\ProgressNoteRequest;
use App\Http\Requests\VitalsRequest;
use App\Models\HospitalDay;
use App\Models\HospitalOrder;
use App\Models\HospitalStay;
use App\Models\Product;
use App\Models\Clinica;
use App\Models\Patient;
use App\Services\HospitalBillingService;
use App\Services\HospitalStayService;
use App\Services\HospitalTreatmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Support\TenantDatabase;

class HospitalController extends Controller
{
    public function __construct(
        private readonly HospitalStayService $stayService,
        private readonly HospitalTreatmentService $treatmentService,
        private readonly HospitalBillingService $billingService,
    ) {
        $this->middleware(ConnectTenantDB::class);
        $this->middleware(function ($request, $next) {
            $this->ensureTenantConnection();

            return $next($request);
        });
    }

    public function index(): View
    {
        $this->ensureTenantConnection();

        $stays = HospitalStay::with(['patient.owner', 'owner', 'cage'])
            ->orderByDesc('admitted_at')
            ->get();

        return view('hospital.index', compact('stays'));
    }

    public function create(Request $request): View
    {
        $this->ensureTenantConnection();

        $patient = $request->has('patient_id') ? Patient::find($request->integer('patient_id')) : null;

        return view('hospital.admit', compact('patient'));
    }

    public function store(AdmitRequest $request): RedirectResponse
    {
        $this->ensureTenantConnection();

        $stay = $this->stayService->admit($request->validated());

        return redirect()->route('hospital.show', $stay);
    }

    public function show(HospitalStay $stay): View
    {
        $this->ensureTenantConnection();

        $this->stayService->ensureDays($stay);
        $stay->load([
            'patient.owner',
            'owner',
            'cage',
            'charges',
            'days' => function ($query) {
                $query
                    ->orderByDesc('date')
                    ->orderByDesc('day_number')
                    ->with(['orders', 'administrations', 'vitals', 'progressNotes']);
            },
        ]);

        $products = Product::orderBy('name')->get();

        return view('hospital.show', compact('stay', 'products'));
    }

    public function addOrder(OrderRequest $request, HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $data = $request->validated();
        $data['stay_id'] = $stay->id;
        $this->treatmentService->createOrder($data);

        return back()->with('status', 'Orden creada.');
    }

    public function stopOrder(HospitalOrder $order): RedirectResponse
    {
        $this->ensureTenantConnection();

        $this->treatmentService->stopOrder($order);

        return back()->with('status', 'Orden detenida.');
    }

    public function addAdministration(AdministrationRequest $request, HospitalOrder $order): RedirectResponse
    {
        $this->ensureTenantConnection();

        $this->treatmentService->createAdministration($order, $request->validated());

        return back()->with('status', 'Aplicación registrada.');
    }

    public function addVitals(VitalsRequest $request, HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $data = $request->validated();
        /** @var HospitalDay $day */
        $day = $stay->days()->whereDate('date', now()->toDateString())->first() ?? $stay->days()->create([
            'date' => now()->toDateString(),
            'day_number' => $stay->days()->max('day_number') + 1,
        ]);
        $stay->vitals()->create(array_merge($data, ['day_id' => $day->id]));

        return back()->with('status', 'Signos vitales guardados.');
    }

    public function addProgress(ProgressNoteRequest $request, HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $data = $request->validated();
        $day = $stay->days()->whereDate('date', now()->toDateString())->first() ?? $stay->days()->create([
            'date' => now()->toDateString(),
            'day_number' => $stay->days()->max('day_number') + 1,
        ]);

        $stay->progressNotes()->create(array_merge($data, ['day_id' => $day->id]));

        return back()->with('status', 'Nota agregada.');
    }

    public function addCharge(ChargeRequest $request, HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $data = $request->validated();
        $data['stay_id'] = $stay->id;
        $this->billingService->addCharge($data);

        return back()->with('status', 'Cargo agregado.');
    }

    public function generateInvoice(HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $sale = $this->billingService->generateInvoice($stay);

        return back()->with('status', 'Factura generada #' . $sale->id);
    }

    public function discharge(HospitalStay $stay): RedirectResponse
    {
        $this->ensureTenantConnection();

        $this->stayService->discharge($stay);

        return redirect()->route('hospital.index')->with('status', 'Paciente dado de alta.');
    }

    private function ensureTenantConnection(): void
    {
        if (config('database.connections.tenant.database')) {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $clinica = Clinica::resolveForUser($user);
        $database = $clinica->db ?? $user->db;

        abort_unless($database, 403, 'No se pudo determinar la clínica del usuario.');

        TenantDatabase::connect($database);
    }
}
