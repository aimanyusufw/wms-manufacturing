<?php

namespace Database\Factories;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'parent_id' => null,
            'code' => strtoupper($this->faker->unique()->bothify('BIN-##-##')),
            'name' => 'Storage Location ' . $this->faker->bothify('#?'),
            'location_type' => LocationType::Bin,
            'level' => 1,
            'capacity_uom' => 2,
            'max_capacity' => 1000,
            'is_pickable' => true,
            'is_putaway_allowed' => true,
            'is_active' => true,
            'description' => $this->faker->optional()->sentence(),
        ];
    }

    public function staging(): static
    {
        return $this->state(fn(array $attributes) => [
            'code' => strtoupper('STG-' . $this->faker->unique()->bothify('###')),
            'name' => 'Staging Area ' . $this->faker->bothify('#?'),
            'location_type' => LocationType::Staging,
            'is_pickable' => false,
            'capacity_uom' => 2,
            'is_putaway_allowed' => false,
        ]);
    }

    public function receiving(): static
    {
        return $this->state(fn(array $attributes) => [
            'code' => strtoupper('RCV-' . $this->faker->unique()->bothify('###')),
            'name' => 'Receiving Area ' . $this->faker->bothify('#?'),
            'location_type' => LocationType::Receiving,
            'is_pickable' => false,
            'capacity_uom' => 2,
            'is_putaway_allowed' => false,
        ]);
    }
}
