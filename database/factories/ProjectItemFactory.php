<?php

namespace Database\Factories;

use App\Models\Project;
use App\Enums\FlowType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectItem>
 */
class ProjectItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->sentence(3),
            'dimensions' => $this->faker->optional()->randomElement(['10x20 cm', '15x30 cm', '20x40 cm', '25x50 cm']),
            'thickness' => $this->faker->optional()->randomElement(['3 mm', '5 mm', '10 mm', '15 mm', '20 mm']),
            'qty_set' => $this->faker->numberBetween(1, 10),
            'quantity' => $this->faker->numberBetween(1, 100),
            'unit' => $this->faker->randomElement(['pcs', 'set', 'unit', 'box', 'roll']),
            'is_bom_locked' => $this->faker->boolean,
            'is_workflow_locked' => $this->faker->boolean,
            'flow_type' => $this->faker->randomElement([FlowType::OLD->value, FlowType::NEW->value]),
            'warehouse_qty' => $this->faker->numberBetween(0, 50),
            'shipped_qty' => $this->faker->numberBetween(0, 30),
        ];
    }
}
