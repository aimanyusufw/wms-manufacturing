<?php

namespace Database\Factories;

use App\Enums\QcStatus;
use App\Models\GoodsReceipt;
use App\Models\ProductionReceipt;
use App\Models\QualityInspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QualityInspection>
 */
class QualityInspectionFactory extends Factory
{
    protected $model = QualityInspection::class;

    public function definition(): array
    {
        return [
            'goods_receipt_id' => null,
            'production_receipt_id' => null,
            'inspection_number' => strtoupper($this->faker->unique()->bothify('QI-2026-####')),
            'inspection_date' => now(),
            'status' => QcStatus::PASSED,
            'inspected_by' => User::factory(),
            'approved_by' => User::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
