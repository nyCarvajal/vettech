<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\CashReportRepository;
use App\Reports\ExpensesReportRepository;
use App\Reports\GroomingReportRepository;
use App\Reports\InventoryReportRepository;
use App\Reports\OperationsReportRepository;
use App\Reports\PaymentsReportRepository;
use App\Reports\ReportFilters;
use App\Reports\SalesReportRepository;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function __construct(
        private readonly SalesReportRepository $salesRepository,
        private readonly PaymentsReportRepository $paymentsRepository,
        private readonly ExpensesReportRepository $expensesRepository,
        private readonly CashReportRepository $cashRepository,
        private readonly OperationsReportRepository $operationsRepository,
        private readonly GroomingReportRepository $groomingRepository,
        private readonly InventoryReportRepository $inventoryRepository,
    ) {
    }

    public function export(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $report = $request->string('report')->toString();
        $format = $request->string('format', 'csv')->toString();

        if ($format !== 'csv') {
            abort(422, 'Formato no soportado');
        }

        $export = match ($report) {
            'sales' => $this->salesRepository->exportData($filters),
            'payments' => $this->paymentsRepository->exportData($filters),
            'expenses' => $this->expensesRepository->exportData($filters),
            'cash' => $this->cashRepository->exportData($filters),
            'operations' => $this->operationsRepository->exportData($filters),
            'grooming' => $this->groomingRepository->exportData($filters),
            'inventory' => $this->inventoryRepository->exportData($filters),
            default => abort(404),
        };

        $filename = "report-{$report}-" . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($export) {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, $export['headers']);
            foreach ($export['rows'] as $row) {
                fputcsv($handle, array_values($row));
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
