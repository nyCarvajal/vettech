<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\GroomingReportRepository;
use App\Reports\ReportFilters;
use Illuminate\Http\Request;

class GroomingReportController extends Controller
{
    public function __construct(private readonly GroomingReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.grooming.index', [
            'filters' => $filters,
            'data' => $data,
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);

        return response()->json($data['series']);
    }
}
