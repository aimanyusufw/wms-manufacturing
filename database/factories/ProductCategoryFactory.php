<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'code' => strtoupper($this->faker->unique()->bothify('CAT-###')),
            'name' => ucfirst($this->faker->unique()->words(2, true)),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function subCategory(?int $parentId = null): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parentId ?? ProductCategory::factory(),
        ]);
    }
}
