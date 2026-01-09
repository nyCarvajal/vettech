<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\User;
use App\Reports\PaymentsReportRepository;
use App\Reports\ReportFilters;
use Illuminate\Http\Request;

class PaymentsReportController extends Controller
{
    public function __construct(private readonly PaymentsReportRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);
        $data['table'] = $data['table']->paginate(20);

        return view('reports.payments.index', [
            'filters' => $filters,
            'data' => $data,
            'users' => User::query()->select('id', 'nombres')->orderBy('nombres')->get(),
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => ['cash', 'card', 'transfer', 'mixed'],
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $data = $this->repository->summary($filters);

        return response()->json($data['series']);
    }
}
