<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(): View
    {
        $products = Product::paginate(15);
        return view('inventory.products.index', compact('products'));
    }

    public function create(): View
    {
        $costProducts = Product::query()->where('type', '!=', 'servicio')->orderBy('name')->get(['id', 'name', 'cost_avg']);
        return view('inventory.products.create', compact('costProducts'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($this->preparePayload($request->validated()));
        return redirect()->route('products.index')->with('status', 'Producto creado');
    }

    public function edit(Product $product): View
    {
        $costProducts = Product::query()->where('type', '!=', 'servicio')->orderBy('name')->get(['id', 'name', 'cost_avg']);
        return view('inventory.products.edit', compact('product', 'costProducts'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->preparePayload($request->validated()));
        return redirect()->route('products.index')->with('status', 'Producto actualizado');
    }

    private function preparePayload(array $data): array
    {
        if (($data['type'] ?? 'med') === 'servicio') {
            $data['authorized_roles'] = collect($data['authorized_roles'] ?? [])->map(fn ($role) => trim((string) $role))->filter()->values()->all();
            $data['cost_structure'] = collect($data['cost_structure'] ?? [])->map(fn ($row) => [
                'product_id' => isset($row['product_id']) ? (int) $row['product_id'] : null,
                'quantity_available' => (float) ($row['quantity_available'] ?? 0),
                'unit_cost' => (float) ($row['unit_cost'] ?? 0),
                'quantity_used' => (float) ($row['quantity_used'] ?? 0),
                'application_cost' => (float) ($row['application_cost'] ?? 0),
            ])->filter(fn ($row) => $row['product_id'] || $row['quantity_used'] || $row['application_cost'])->values()->all();
        } else {
            $data['estimated_duration_minutes'] = null;
            $data['authorized_roles'] = null;
            $data['cost_structure'] = null;
            $data['cost_structure_commission_percent'] = null;
        }

        return $data;
    }
}
