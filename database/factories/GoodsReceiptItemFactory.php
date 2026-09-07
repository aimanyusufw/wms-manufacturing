<?php

namespace Database\Factories;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoodsReceiptItem>
 */
class GoodsReceiptItemFactory extends Factory
{
    protected $model = GoodsReceiptItem::class;

    public function definition(): array
    {
        $received = $this->faker->numberBetween(10, 50);

        return [
            'goods_receipt_id' => GoodsReceipt::factory(),
            'purchase_order_item_id' => null,
            'product_id' => Product::factory(),
            'uom_id' => Uom::factory(),
            'lot_id' => null,
            'pallet_id' => null,
            'received_qty' => $received,
            'accepted_qty' => $received,
            'rejected_qty' => 0,
            'notes' => null,
        ];
    }
}
