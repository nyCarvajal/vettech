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
use App\Reports\VaccinesReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;
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
        private readonly VaccinesReportRepository $vaccinesRepository,
    ) {
    }

    public function export(Request $request)
    {
        $filters = ReportFilters::fromRequest($request);
        $report = $request->string('report')->toString();
        $format = $request->string('format', 'csv')->toString();

        $extraFilters = [
            'rabies' => $request->string('rabies', 'all')->toString(),
            'source' => $request->string('source', 'all')->toString(),
            'q' => $request->string('q')->toString(),
        ];

        $export = match ($report) {
            'sales' => $this->salesRepository->exportData($filters),
            'payments' => $this->paymentsRepository->exportData($filters),
            'expenses' => $this->expensesRepository->exportData($filters),
            'cash' => $this->cashRepository->exportData($filters),
            'operations' => $this->operationsRepository->exportData($filters),
            'grooming' => $this->groomingRepository->exportData($filters),
            'inventory' => $this->inventoryRepository->exportData($filters),
            'vaccines' => $this->vaccinesRepository->exportData($filters, $extraFilters),
            default => abort(404),
        };

        if ($format === 'csv') {
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

        if ($report === 'vaccines' && $format === 'excel') {
            $filename = "report-{$report}-" . now()->format('Ymd_His') . '.xls';

            return response()->make(
                view('reports.vaccines.excel', ['headers' => $export['headers'], 'rows' => $export['rows']])->render(),
                200,
                [
                    'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                    'Content-Disposition' => "attachment; filename={$filename}",
                ]
            );
        }

        if ($report === 'vaccines' && $format === 'pdf') {
            $records = $this->vaccinesRepository->recordsForPdf($filters, $extraFilters);
            $pdf = Pdf::loadView('reports.vaccines.pdf', [
                'records' => $records,
                'filters' => $filters,
                'extraFilters' => $extraFilters,
            ])->setPaper('a4');

            return $pdf->download("report-{$report}-" . now()->format('Ymd_His') . '.pdf');
        }

        abort(422, 'Formato no soportado');
    }
}
