<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuickCreateItemRequest;
use App\Models\Item;

class SupplierItemController extends Controller
{
    public function store(QuickCreateItemRequest $request)
    {
        $item = Item::create([
            'nombre' => $request->string('nombre')->toString(),
            'sku' => $request->input('sku'),
            'cost_price' => $request->input('cost_price', 0),
            'sale_price' => $request->input('sale_price', 0),
            'stock' => $request->input('stock', 0),
            'stock_minimo' => $request->input('stock_minimo', 0),
            'cantidad' => $request->input('stock_minimo', 0),
            'costo' => $request->input('cost_price', 0),
            'valor' => $request->input('sale_price', 0),
            'track_inventory' => true,
            'inventariable' => true,
            'estado' => $request->input('estado', 'activo'),
            'type' => 'product',
        ]);

        return response()->json([
            'id' => $item->id,
            'nombre' => $item->nombre,
            'cost_price' => $item->cost_price,
            'sale_price' => $item->sale_price,
        ]);
    }
}
