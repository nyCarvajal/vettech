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
        return view('inventory.products.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());
        return redirect()->route('products.index')->with('status', 'Producto creado');
    }

    public function edit(Product $product): View
    {
        return view('inventory.products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());
        return redirect()->route('products.index')->with('status', 'Producto actualizado');
    }
}
