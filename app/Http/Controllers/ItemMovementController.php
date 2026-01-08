<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Item;
use App\Services\Inventory\InventoryService;

class ItemMovementController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function index(Item $item)
    {
        $movements = $item->inventoryMovements()
            ->latest('occurred_at')
            ->paginate(20);

        return view('items.movements.index', compact('item', 'movements'));
    }

    public function entry(StoreMovementRequest $request, Item $item)
    {
        $data = $request->validated();

        $this->inventoryService->addEntry($item, (float) $data['quantity'], $data);

        return back()->with('success', 'Entrada registrada correctamente.');
    }

    public function exit(StoreMovementRequest $request, Item $item)
    {
        $data = $request->validated();

        $this->inventoryService->addExit($item, (float) $data['quantity'], $data);

        return back()->with('success', 'Salida registrada correctamente.');
    }

    public function adjust(StoreMovementRequest $request, Item $item)
    {
        $data = $request->validated();

        $this->inventoryService->addAdjust($item, (float) $data['quantity'], $data);

        return back()->with('success', 'Ajuste registrado correctamente.');
    }
}
