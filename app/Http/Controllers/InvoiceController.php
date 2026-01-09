<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\VoidInvoiceRequest;
use App\Models\Invoice;
use App\Models\Banco;
use App\Models\Owner;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

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

    public function create()
    {
        $owners = Owner::query()->orderBy('name')->limit(50)->get();
        $banks = Banco::query()->orderBy('nombre')->get();

        return view('invoices.pos', compact('owners', 'banks'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factura creada correctamente.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['owner', 'lines.item', 'payments']);

        return view('invoices.show', compact('invoice'));
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
