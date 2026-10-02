<?php

namespace Database\Factories;

use App\Models\InventorySparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventorySparepart>
 */
class InventorySparepartFactory extends Factory
{
    protected $model = InventorySparepart::class;

    public function definition(): array
    {
        $buyPrice = fake()->numberBetween(50000, 800000);
        $sellPrice = $buyPrice + fake()->numberBetween(50000, 400000);

        return [
            'part_name' => fake()->randomElement(['LCD iPhone 11', 'Baterai iPhone 12', 'Port Charger Samsung S20', 'Kaca Kamera Xiaomi Note 10', 'Speaker iPhone X', 'IC Power Universal']),
            'part_code' => 'SP-'.strtoupper(fake()->bothify('??-####')),
            'category' => fake()->randomElement(['LCD', 'Baterai', 'Fleksibel', 'Kamera', 'IC & Mesin']),
            'stock' => fake()->numberBetween(0, 30),
            'min_stock' => 5,
            'buy_price' => $buyPrice,
            'sell_price' => $sellPrice,
            'description' => fake()->sentence(),
        ];
    }
}
