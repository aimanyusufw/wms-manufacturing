<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionReceiptItem>
 */
class ProductionReceiptItemFactory extends Factory
{
    protected $model = ProductionReceiptItem::class;

    public function definition(): array
    {
        $received = $this->faker->numberBetween(15, 60);

        return [
            'production_receipt_id' => ProductionReceipt::factory(),
            'product_id' => Product::factory(),
            'uom_id' => Uom::factory(),
            'lot_id' => null,
            'pallet_id' => null,
            'destination_bin_id' => null,
            'received_qty' => $received,
            'rejected_qty' => 0,
            'notes' => null,
        ];
    }
}
