<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\User;
use App\Reports\ReportFilters;
use App\Reports\SalesReportRepository;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function __construct(private readonly SalesReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.sales.index', [
            'filters' => $filters,
            'data' => $data,
            'users' => User::query()->select('id', 'nombres')->orderBy('nombres')->get(),
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
