<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('WH-##')),
            'name' => 'Gudang ' . ucfirst($this->faker->unique()->city()),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
