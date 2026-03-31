<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Usuario;
use App\Reports\ReportFilters;
use App\Reports\VaccinesReportRepository;
use Illuminate\Http\Request;

class VaccinesReportController extends Controller
{
    public function __construct(private readonly VaccinesReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $extraFilters = [
            'rabies' => $request->string('rabies', 'all')->toString(),
            'source' => $request->string('source', 'all')->toString(),
            'q' => $request->string('q')->toString(),
        ];

        $data = $this->repository->summary($filters, $extraFilters);
        $data['table'] = $data['table']->paginate(20)->withQueryString();

        return view('reports.vaccines.index', [
            'filters' => $filters,
            'extraFilters' => $extraFilters,
            'data' => $data,
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $extraFilters = [
            'rabies' => $request->string('rabies', 'all')->toString(),
            'source' => $request->string('source', 'all')->toString(),
            'q' => $request->string('q')->toString(),
        ];

        $data = $this->repository->summary($filters, $extraFilters);

        return response()->json([
            'series' => $data['series'],
            'statusBreakdown' => $data['status_breakdown'],
        ]);
    }
}
