<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\User;
use App\Reports\ExpensesReportRepository;
use App\Reports\PaymentsReportRepository;
use App\Reports\ReportFilters;
use App\Reports\SalesReportRepository;
use Illuminate\Http\Request;

class QuickReportsController extends Controller
{
    public function __construct(
        private readonly SalesReportRepository $salesRepository,
        private readonly PaymentsReportRepository $paymentsRepository,
        private readonly ExpensesReportRepository $expensesRepository,
    ) {
    }

    public function index(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);

        $sales = $this->salesRepository->summary($filters);
        $payments = $this->paymentsRepository->summary($filters);
        $expenses = $this->expensesRepository->summary($filters);

        return view('reports.quick.index', [
            'filters' => $filters,
            'sales' => $sales,
            'payments' => $payments,
            'expenses' => $expenses,
            'users' => User::query()->select('id', 'nombres')->orderBy('nombres')->get(),
            'owners' => Owner::query()->select('id', 'name')->orderBy('name')->get(),
            'paymentMethods' => ['cash', 'card', 'transfer', 'mixed'],
        ]);
    }

    public function data(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);

        $sales = $this->salesRepository->summary($filters);
        $payments = $this->paymentsRepository->summary($filters);
        $expenses = $this->expensesRepository->summary($filters);

        return response()->json([
            'sales' => $sales['series'],
            'commissions' => $sales['commission_series'],
            'payments' => $payments['series'],
            'expenses' => $expenses['series'],
            'kpis' => [
                'sales_total' => $sales['kpis']['total_sales'],
                'payments_total' => $payments['kpis']['total_payments'],
                'commissions_total' => $sales['kpis']['commissions_total'],
                'expenses_total' => $expenses['kpis']['total_expenses'],
            ],
        ]);
    }
}
