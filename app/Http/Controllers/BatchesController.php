<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchRequest;
use App\Models\Batch;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BatchesController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function index(): View
    {
        $batches = Batch::with('product')->latest()->paginate(15);
        return view('inventory.batches.index', compact('batches'));
    }

    public function create(): View
    {
        $products = Product::where('requires_batch', true)->get();
        return view('inventory.batches.create', compact('products'));
    }

    public function store(BatchRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $batch = Batch::create($data + ['qty_out' => 0, 'qty_available' => $data['qty_in']]);
        $product = $batch->product;
        $this->inventoryService->moveStock($product, $batch, 'in', $data['qty_in'], 'ingreso inicial', 'batch', $batch->id, $request->user());

        return redirect()->route('batches.index')->with('status', 'Lote creado');
    }
}
