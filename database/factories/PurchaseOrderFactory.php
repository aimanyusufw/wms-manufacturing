<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'warehouse_id' => Warehouse::factory(),
            'document_number' => strtoupper($this->faker->unique()->bothify('PO-2026-####')),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'expected_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => PurchaseOrderStatus::APPROVED,
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ];
    }
}
