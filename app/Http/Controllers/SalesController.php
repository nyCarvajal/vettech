<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('items')->latest()->paginate(15);
        return view('sales.index', compact('sales'));
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        $sale = Sale::create($request->validated() + ['created_by' => $request->user()->id, 'total' => 0]);
        return redirect()->route('sales.show', $sale);
    }

    public function show(Sale $sale): View
    {
        $sale->load('items');
        return view('sales.show', compact('sale'));
    }
}
