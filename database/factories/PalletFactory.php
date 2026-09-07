<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Pallet;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pallet>
 */
class PalletFactory extends Factory
{
    protected $model = Pallet::class;

    public function definition(): array
    {
        return [
            'pallet_code' => strtoupper($this->faker->unique()->bothify('PLT-####-??')),
            'warehouse_id' => Warehouse::factory(),
            'location_id' => null,
            'pallet_type' => $this->faker->randomElement(['Wood Standard', 'Plastic Heavy Duty', 'Euro Pallet']),
            'weight' => $this->faker->randomFloat(2, 15, 30),
            'status' => 'active',
        ];
    }
}
