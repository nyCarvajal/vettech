<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CashClosure;
use App\Reports\CashReportRepository;
use App\Reports\ReportFilters;
use Illuminate\Http\Request;

class CashReportController extends Controller
{
    public function __construct(private readonly CashReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.cash.index', [
            'filters' => $filters,
            'data' => $data,
            'paymentMethods' => ['cash', 'card', 'transfer', 'mixed'],
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);

        return response()->json([
            'income' => $data['series'],
            'expenses' => $data['expense_series'],
        ]);
    }

    public function storeClosure(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'expected_cash' => ['required', 'numeric'],
            'counted_cash' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data['difference'] = $data['counted_cash'] - $data['expected_cash'];
        $data['user_id'] = $request->user()->id;
        $data['tenant_id'] = $request->user()->tenant_id ?? null;

        CashClosure::create($data);

        return redirect()->back()->with('success', 'Arqueo registrado.');
    }
}
