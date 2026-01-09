<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\Area;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    /**
     * Mostrar listado de items.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'tipo', 'area', 'status']);

        $items = Item::with('areaRelation')
            ->filter($filters)
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        $selectedItem = null;
        if ($request->filled('selected')) {
            $selectedItem = Item::with([
                'areaRelation',
                'inventoryMovements' => fn ($query) => $query->latest('occurred_at')->limit(6),
            ])->find($request->selected);
        } elseif ($items->count() > 0) {
            $selectedItem = Item::with([
                'areaRelation',
                'inventoryMovements' => fn ($query) => $query->latest('occurred_at')->limit(6),
            ])->find($items->first()->id);
        }

        $areas = Area::orderBy('descripcion')->get();
        $categoryOptions = Item::query()
            ->select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return view('items.index', compact('items', 'areas', 'categoryOptions', 'selectedItem'));
    }

    /**
     * Mostrar formulario para crear un nuevo item.
     */
    public function create()
    {
        $areas = Area::orderBy('descripcion')->get();
        $categoryOptions = Item::query()
            ->select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return view('items.create', compact('areas', 'categoryOptions'));
    }

    /**
     * Guardar un item nuevo en la base de datos.
     */
    public function store(StoreItemRequest $request)
    {
        $payload = $this->prepareItemPayload($request->validated());
        $this->inventoryService->createItemWithInitialStock($payload);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item creado correctamente.');
    }

    /**
     * Mostrar un item en particular.
     */
    public function show(Item $item)
    {
        $item->load('areaRelation');
        $movements = $item->inventoryMovements()
            ->latest('occurred_at')
            ->paginate(15);

        if (request()->wantsJson()) {
            return response()->json([
                'item' => $item,
                'status_label' => $item->status_label,
                'status_color' => $item->status_color,
                'movements' => $movements,
            ]);
        }

        return view('items.show', compact('item', 'movements'));
    }

    /**
     * Mostrar formulario para editar un item existente.
     */
    public function edit(Item $item)
    {
        $areas = Area::orderBy('descripcion')->get();
        $categoryOptions = Item::query()
            ->select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return view('items.edit', compact('item', 'areas', 'categoryOptions'));
    }

    /**
     * Actualizar un item existente.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $payload = $this->prepareItemPayload($request->validated());
        $newStock = array_key_exists('stock', $payload) ? (float) ($payload['stock'] ?? 0) : null;

        unset($payload['stock']);

        $item->update($payload);

        if ($newStock !== null) {
            $currentStock = (float) $item->stock;
            $delta = $newStock - $currentStock;
            if (abs($delta) > 0.0001) {
                $this->inventoryService->addAdjust($item, $delta, [
                    'reference' => 'Ajuste por edición',
                ]);
            }
        }

        return redirect()
            ->route('items.index')
            ->with('success', 'Item actualizado correctamente.');
    }

    private function prepareItemPayload(array $data): array
    {
        $data['sale_price'] = $this->normalizeCurrency($data['sale_price'] ?? null);
        $data['cost_price'] = $this->normalizeCurrency($data['cost_price'] ?? null);
        $data['valor'] = $data['sale_price'];
        $data['costo'] = $data['cost_price'];

        $data['inventariable'] = (bool) ($data['inventariable'] ?? false);
        $data['track_inventory'] = (bool) ($data['track_inventory'] ?? false);

        if (($data['type'] ?? 'product') === 'service') {
            $data['inventariable'] = false;
            $data['track_inventory'] = false;
        }

        return $data;
    }

    private function normalizeCurrency($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^\d,.-]/', '', (string) $value);
        $normalized = str_replace('.', '', $normalized);
        $normalized = str_replace(',', '.', $normalized);

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    /**
     * Eliminar un item.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item eliminado correctamente.');
    }
}
