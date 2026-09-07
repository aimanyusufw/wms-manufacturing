<?php

namespace Database\Factories;

use App\Models\Lot;
use App\Models\Product;
use App\Models\QualityInspection;
use App\Models\QualityInspectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QualityInspectionItem>
 */
class QualityInspectionItemFactory extends Factory
{
    protected $model = QualityInspectionItem::class;

    public function definition(): array
    {
        $qty = $this->faker->numberBetween(10, 50);

        return [
            'quality_inspection_id' => QualityInspection::factory(),
            'product_id' => Product::factory(),
            'lot_id' => null,
            'inspected_qty' => $qty,
            'passed_qty' => $qty,
            'failed_qty' => 0,
            'remarks' => 'Quality verified standard compliance',
        ];
    }
}
