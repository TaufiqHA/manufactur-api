<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\ProjectItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubAssembly>
 */
class SubAssemblyFactory extends Factory
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
            'name' => $this->faker->word(),
            'qty_per_parent' => $this->faker->numberBetween(1, 100),
            'total_needed' => $this->faker->numberBetween(0, 1000),
            'completed_qty' => $this->faker->numberBetween(0, 500),
            'total_produced' => $this->faker->numberBetween(0, 500),
            'consumed_qty' => $this->faker->numberBetween(0, 500),
            'material_id' => $this->faker->boolean ? Material::factory() : null,
            'processes' => json_encode([
                [
                    'name' => $this->faker->word(),
                    'duration' => $this->faker->randomElement([30, 60, 90, 120]),
                    'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
                ],
                [
                    'name' => $this->faker->word(),
                    'duration' => $this->faker->randomElement([30, 60, 90, 120]),
                    'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
                ]
            ]),
            'step_stats' => json_encode([
                'total_steps' => $this->faker->numberBetween(1, 10),
                'completed_steps' => $this->faker->numberBetween(0, 10),
                'pending_steps' => $this->faker->numberBetween(0, 10),
            ]),
            'is_locked' => $this->faker->boolean(),
        ];
    }
}
