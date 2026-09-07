<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductUom>
 */
class ProductUomFactory extends Factory
{
    protected $model = ProductUom::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'uom_id' => Uom::factory(),
            'conversion_factor' => $this->faker->randomElement([1, 10, 12, 24, 50, 100]),
            'is_purchase_uom' => $this->faker->boolean(50),
            'is_sales_uom' => $this->faker->boolean(50),
        ];
    }
}
