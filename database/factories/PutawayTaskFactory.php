<?php

namespace Database\Factories;

use App\Enums\PutawayStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\PutawayTask;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PutawayTask>
 */
class PutawayTaskFactory extends Factory
{
    protected $model = PutawayTask::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'goods_receipt_id' => null,
            'goods_receipt_item_id' => null,
            'production_receipt_id' => null,
            'production_receipt_item_id' => null,
            'source_type' => null,
            'source_id' => null,
            'product_id' => Product::factory(),
            'lot_id' => null,
            'pallet_id' => null,
            'source_bin_id' => Location::factory(),
            'destination_bin_id' => Location::factory(),
            'qty' => $this->faker->numberBetween(10, 50),
            'status' => PutawayStatus::PENDING,
            'assigned_to' => null,
            'completed_by' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PutawayStatus::COMPLETED,
            'completed_by' => User::factory(),
            'completed_at' => now(),
        ]);
    }
}
