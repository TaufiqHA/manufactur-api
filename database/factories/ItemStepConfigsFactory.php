<?php

namespace Database\Factories;

use App\Models\ItemStepConfigs;
use App\Models\ProjectItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemStepConfigs>
 */
class ItemStepConfigsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => ProjectItem::factory(), // Create or use an existing ProjectItem
            'step' => $this->faker->word(),
            'sequence' => $this->faker->numberBetween(1, 100),
        ];
    }
}
