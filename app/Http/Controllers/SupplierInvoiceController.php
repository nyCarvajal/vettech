<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierInvoiceStoreRequest;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\Suppliers\SupplierInvoiceService;
use Illuminate\Http\Request;

class SupplierInvoiceController extends Controller
{
    public function __construct(private readonly SupplierInvoiceService $service)
    {
    }

    public function index(Request $request)
    {
        $invoices = SupplierInvoice::query()
            ->with('supplier')
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->integer('supplier_id')))
            ->when($request->filled('estado_pago'), fn ($q) => $q->where('estado_pago', $request->string('estado_pago')))
            ->when($request->filled('fecha_inicio'), fn ($q) => $q->whereDate('fecha_factura', '>=', $request->date('fecha_inicio')))
            ->when($request->filled('fecha_fin'), fn ($q) => $q->whereDate('fecha_factura', '<=', $request->date('fecha_fin')))
            ->when($request->boolean('vencidas'), fn ($q) => $q->vencidas())
            ->latest('fecha_factura')
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::query()->orderBy('razon_social')->get();

        return view('supplier_invoices.index', compact('invoices', 'suppliers'));
    }

    public function create()
    {
        return view('supplier_invoices.create', [
            'suppliers' => Supplier::where('estado', 'activo')->orderBy('razon_social')->get(),
            'items' => Item::where('estado', 'activo')->orderBy('nombre')->limit(200)->get(),
        ]);
    }

    public function store(SupplierInvoiceStoreRequest $request)
    {
        $invoice = $this->service->create($request->validated(), (int) auth()->id());

        return redirect()->route('supplier-invoices.show', $invoice)->with('success', 'Factura registrada correctamente.');
    }

    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'details.item', 'payments']);

        return view('supplier_invoices.show', ['invoice' => $supplierInvoice]);
    }

    public function dueAlerts(Request $request)
    {
        $days = max(1, min(30, (int) $request->integer('days', 5)));

        $overdue = SupplierInvoice::with('supplier')->vencidas()->where('estado', 'confirmada')->get();
        $dueToday = SupplierInvoice::with('supplier')->pendientes()->where('estado', 'confirmada')->whereDate('fecha_vencimiento', now())->get();
        $upcoming = SupplierInvoice::with('supplier')->porVencer($days)->where('estado', 'confirmada')->whereDate('fecha_vencimiento', '>', now())->get();

        return view('supplier_invoices.due_alerts', compact('overdue', 'dueToday', 'upcoming', 'days'));
    }

    public function cancel(SupplierInvoice $supplierInvoice)
    {
        $this->service->cancel($supplierInvoice->load('details'), (int) auth()->id());

        return back()->with('success', 'Factura anulada y stock revertido correctamente.');
    }
}
