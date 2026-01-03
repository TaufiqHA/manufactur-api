<?php

namespace Database\Factories;

use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfigs;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StepStockBalance>
 */
class StepStockBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => ProjectItem::factory(),
            'sub_assembly_id' => SubAssembly::factory(),
            'process_step_id' => ItemStepConfigs::factory(),
            'total_produced' => $this->faker->numberBetween(0, 1000),
            'total_consumed' => $this->faker->numberBetween(0, 1000),
            'available_qty' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
