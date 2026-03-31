<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierPaymentStoreRequest;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\Suppliers\SupplierPaymentService;

class SupplierPaymentController extends Controller
{
    public function __construct(private readonly SupplierPaymentService $service)
    {
    }

    public function index()
    {
        $payments = SupplierPayment::with(['supplier', 'invoice'])->latest('fecha_pago')->paginate(20);

        return view('supplier_payments.index', compact('payments'));
    }

    public function create()
    {
        return view('supplier_payments.create', [
            'suppliers' => Supplier::orderBy('razon_social')->get(),
            'invoices' => \App\Models\SupplierInvoice::pendientes()->where('estado', 'confirmada')->orderBy('fecha_vencimiento')->get(),
            'cajas' => Caja::orderByDesc('id')->get(),
            'bancos' => Banco::orderBy('nombre')->get(),
        ]);
    }

    public function store(SupplierPaymentStoreRequest $request)
    {
        $payment = $this->service->create($request->validated(), (int) auth()->id());

        return redirect()->route('supplier-payments.show', $payment)->with('success', 'Pago registrado correctamente.');
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load(['supplier', 'invoice', 'caja', 'banco']);

        return view('supplier_payments.show', ['payment' => $supplierPayment]);
    }
}
