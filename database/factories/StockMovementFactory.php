<?php

namespace Database\Factories;

use App\Enums\MovementDirection;
use App\Enums\StockMovementType;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(10, 50);

        return [
            'movement_number' => strtoupper($this->faker->unique()->bothify('SM-2026-####')),
            'movement_date' => now(),
            'warehouse_id' => Warehouse::factory(),
            'bin_id' => Location::factory(),
            'product_id' => Product::factory(),
            'lot_id' => null,
            'pallet_id' => null,
            'movement_type' => StockMovementType::RECEIPT,
            'direction' => MovementDirection::IN,
            'quantity' => $qty,
            'balance_after' => $qty,
            'reference_type' => null,
            'reference_id' => null,
            'grn_id' => null,
            'production_receipt_id' => null,
            'performed_by' => User::factory(),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
        ];
    }
}
