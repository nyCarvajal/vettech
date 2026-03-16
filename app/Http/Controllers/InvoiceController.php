<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StoreInvoicePaymentsRequest;
use App\Http\Requests\VoidInvoiceRequest;
use App\Models\Invoice;
use App\Models\Banco;
use App\Models\Owner;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    public function index(Request $request)
    {
        $query = Invoice::query()->with(['owner']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->integer('owner_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('issued_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('issued_at', '<=', $request->date('to'));
        }

        $invoices = $query->latest('issued_at')->paginate(15)->withQueryString();
        $owners = Owner::query()->orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'owners'));
    }

    public function create(Request $request)
    {
        $owners = Owner::query()->orderBy('name')->limit(50)->get();
        $banks = Banco::query()->orderBy('nombre')->get();
        $prefillInvoice = null;

        if ($request->filled('from_invoice')) {
            $prefillInvoice = Invoice::query()
                ->with(['owner', 'lines.item', 'payments'])
                ->findOrFail($request->integer('from_invoice'));

            $this->authorize('view', $prefillInvoice);
        }

        return view('invoices.pos', compact('owners', 'banks', 'prefillInvoice'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factura creada correctamente.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['owner', 'lines.item', 'payments']);
        $banks = Banco::query()->orderBy('nombre')->get();

        return view('invoices.show', compact('invoice', 'banks'));
    }


    public function storePayments(StoreInvoicePaymentsRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'void') {
            return redirect()->route('invoices.show', $invoice)->withErrors([
                'payments' => 'No se pueden registrar pagos en una factura anulada.',
            ]);
        }

        DB::connection('tenant')->transaction(function () use ($invoice, $request) {
            $paidTotal = (float) $invoice->paid_total;
            $changeTotal = (float) $invoice->change_total;

            foreach ($request->validated('payments') as $payment) {
                $amount = (float) ($payment['amount'] ?? 0);
                $received = isset($payment['received']) ? (float) $payment['received'] : null;
                $change = 0;

                if (($payment['method'] ?? '') === 'cash') {
                    $received = $received ?? $amount;
                    $change = max(0, $received - $amount);
                }

                $invoice->payments()->create([
                    'method' => $payment['method'],
                    'amount' => $amount,
                    'received' => $received,
                    'change' => $change,
                    'reference' => $payment['reference'] ?? null,
                    'card_type' => $payment['card_type'] ?? null,
                    'bank_id' => $payment['bank_id'] ?? null,
                    'paid_at' => now(),
                ]);

                $paidTotal += $amount;
                $changeTotal += $change;
            }

            $invoice->update([
                'paid_total' => round($paidTotal, 2),
                'change_total' => round($changeTotal, 2),
                'status' => $paidTotal >= (float) $invoice->total ? 'paid' : 'issued',
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Pagos registrados correctamente.');
    }

    public function void(VoidInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('void', $invoice);
        $invoice = $this->invoiceService->voidInvoice($invoice, $request->string('reason'));

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factura anulada correctamente.');
    }

    public function print(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['owner', 'lines.item', 'payments']);

        return view('invoices.print', compact('invoice'));
    }
}
