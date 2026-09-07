<?php

namespace Database\Factories;

use App\Models\Uom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Uom>
 */
class UomFactory extends Factory
{
    protected $model = Uom::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'name' => ucfirst($this->faker->unique()->word()),
        ];
    }
}
