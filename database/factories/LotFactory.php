<?php

namespace Database\Factories;

use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lot>
 */
class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        $mfgDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $expiryDate = (clone $mfgDate)->modify('+1 year');

        return [
            'product_id' => Product::factory(),
            'lot_number' => strtoupper($this->faker->unique()->bothify('LOT-####-??')),
            'manufacture_date' => $mfgDate,
            'expiry_date' => $expiryDate,
            'status' => LotStatus::ACTIVE,
        ];
    }
}
