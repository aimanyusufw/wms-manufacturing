<?php

namespace Database\Factories;

use App\Enums\ProductType;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => ProductCategory::factory(),
            'base_uom_id' => Uom::factory(),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####-??')),
            'barcode' => $this->faker->unique()->ean13(),
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'product_type' => $this->faker->randomElement(ProductType::cases()),
            'description' => $this->faker->sentence(),
            'min_stock' => 10,
            'max_stock' => 500,
            'reorder_point' => 25,
            'shelf_life_days' => 365,
            'track_lot' => true,
            'track_serial' => false,
            'is_active' => true,
        ];
    }
}
