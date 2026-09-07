<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Models\ProductionReceipt;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionReceipt>
 */
class ProductionReceiptFactory extends Factory
{
    protected $model = ProductionReceipt::class;

    public function definition(): array
    {
        return [
            'document_number' => strtoupper($this->faker->unique()->bothify('PR-2026-####')),
            'production_order_id' => null,
            'warehouse_id' => Warehouse::factory(),
            'receipt_date' => now(),
            'status' => DocumentStatus::APPROVED,
            'received_by' => User::factory(),
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
