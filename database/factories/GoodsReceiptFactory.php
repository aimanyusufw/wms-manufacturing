<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodsReceipt>
 */
class GoodsReceiptFactory extends Factory
{
    protected $model = GoodsReceipt::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'supplier_id' => Supplier::factory(),
            'warehouse_id' => Warehouse::factory(),
            'document_number' => strtoupper($this->faker->unique()->bothify('GRN-2026-####')),
            'receipt_date' => now(),
            'status' => DocumentStatus::APPROVED,
            'delivery_note_number' => strtoupper($this->faker->bothify('SJ-#####')),
            'received_by' => User::factory(),
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
