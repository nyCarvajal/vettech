<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCashClosureRequest;
use App\Models\CashClosure;
use App\Services\CashClosureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashClosureController extends Controller
{
    public function __construct(private readonly CashClosureService $service)
    {
    }

    public function index(Request $request): View
    {
        $query = CashClosure::query()->with('user');

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->string('from')->toString());
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->string('to')->toString());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        $closures = $query->orderByDesc('date')->paginate(15)->withQueryString();

        return view('cash_closures.index', compact('closures'));
    }

    public function create(Request $request): View
    {
        $date = $request->string('date', now()->toDateString())->toString();
        $clinicId = $request->user()?->clinica_id;

        $summary = $this->service->getSummary($date, $clinicId);
        $closure = CashClosure::where('date', $date)
            ->where('clinica_id', $clinicId)
            ->first();

        return view('cash_closures.create', compact('summary', 'closure'));
    }

    public function store(StoreCashClosureRequest $request): RedirectResponse
    {
        $clinicId = $request->user()?->clinica_id;
        $summary = $this->service->getSummary($request->string('date')->toString(), $clinicId);

        $closure = DB::transaction(function () use ($request, $summary, $clinicId) {
            return $this->service->storeClosure(
                $request->validated(),
                $summary,
                $request->user()->id,
                $clinicId
            );
        });

        return redirect()
            ->route('cash.closures.show', $closure)
            ->with('status', 'Cierre guardado correctamente.');
    }

    public function show(CashClosure $closure): View
    {
        $summary = $this->service->getSummary($closure->date->toDateString(), $closure->clinica_id);

        return view('cash_closures.show', compact('closure', 'summary'));
    }

    public function print(CashClosure $closure): View
    {
        $summary = $this->service->getSummary($closure->date->toDateString(), $closure->clinica_id);
        $clinic = request()->user()?->clinica;

        return view('cash_closures.print', compact('closure', 'summary', 'clinic'));
    }

    public function summary(Request $request): JsonResponse
    {
        $clinicId = $request->user()?->clinica_id;
        $date = $request->string('date', now()->toDateString())->toString();

        return response()->json($this->service->getSummary($date, $clinicId));
    }
}
