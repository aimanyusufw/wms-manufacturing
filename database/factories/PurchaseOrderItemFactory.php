<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(10, 100);
        $unitPrice = $this->faker->numberBetween(10000, 500000);
        $lineTotal = $qty * $unitPrice;

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'uom_id' => Uom::factory(),
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'line_total' => $lineTotal,
        ];
    }
}
