<?php

namespace Database\Factories;

use App\Enums\InventoryStatus;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryBalance>
 */
class InventoryBalanceFactory extends Factory
{
    protected $model = InventoryBalance::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'bin_id' => Location::factory(),
            'product_id' => Product::factory(),
            'lot_id' => null,
            'pallet_id' => null,
            'inventory_status' => InventoryStatus::AVAILABLE,
            'quantity' => $this->faker->numberBetween(50, 200),
            'reserved_quantity' => 0,
            'last_movement_at' => now(),
        ];
    }
}
