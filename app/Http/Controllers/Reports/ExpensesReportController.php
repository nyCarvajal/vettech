<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Reports\ExpensesReportRepository;
use App\Reports\ReportFilters;
use Illuminate\Http\Request;

class ExpensesReportController extends Controller
{
    public function __construct(private readonly ExpensesReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.expenses.index', [
            'filters' => $filters,
            'data' => $data,
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);

        return response()->json($data['series']);
    }
}
