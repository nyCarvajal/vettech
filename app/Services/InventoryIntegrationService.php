<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryIntegrationService
{
    public function validateStock(int $productId): Product
    {
        $product = Product::findOrFail($productId);

        if (method_exists($product, 'hasStock') && !$product->hasStock()) {
            throw ValidationException::withMessages([
                'product_id' => 'Stock insuficiente para el producto seleccionado.',
            ]);
        }

        return $product;
    }

    public function deductStock(array $movements): void
    {
        // V1: sin integración automática; el método queda documentado para futura extensión.
        // Se puede conectar con InventoryService o DispenseService si se requiere.
    }
}
