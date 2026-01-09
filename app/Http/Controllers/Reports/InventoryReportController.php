<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\InventoryReportRepository;
use App\Reports\ReportFilters;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function __construct(private readonly InventoryReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $itemId = $request->integer('item_id') ?: null;
        $data = $this->repository->summary($filters, $itemId);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.inventory.index', [
            'filters' => $filters,
            'data' => $data,
            'itemId' => $itemId,
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $itemId = $request->integer('item_id') ?: null;
        $data = $this->repository->summary($filters, $itemId);

        return response()->json($data['series']);
    }
}
