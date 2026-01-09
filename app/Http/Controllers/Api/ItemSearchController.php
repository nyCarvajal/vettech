<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->string('q')->toString();

        $items = Item::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('nombre', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                });
            })
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return response()->json($items->map(fn (Item $item) => [
            'id' => $item->id,
            'text' => $item->nombre,
            'price' => $item->sale_price ?? $item->valor ?? 0,
            'stock' => $item->stock,
            'track_inventory' => $item->track_inventory,
        ]));
    }
}
