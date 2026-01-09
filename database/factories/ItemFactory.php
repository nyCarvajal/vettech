<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(2, true),
            'cantidad' => 0,
            'costo' => $this->faker->randomFloat(2, 1000, 50000),
            'valor' => $this->faker->randomFloat(2, 1500, 80000),
            'tipo' => 1,
            'area' => 1,
            'type' => 'product',
            'sku' => $this->faker->bothify('SKU-###'),
            'stock' => 10,
            'track_inventory' => true,
            'sale_price' => $this->faker->randomFloat(2, 1500, 80000),
            'cost_price' => $this->faker->randomFloat(2, 1000, 50000),
        ];
    }
}
