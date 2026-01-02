<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => $this->faker->uuid,
            'project_name' => $this->faker->sentence(3),
            'item_id' => $this->faker->uuid,
            'item_name' => $this->faker->sentence(2),
            'sub_assembly_id' => $this->faker->optional(0.5, null)->uuid, // 50% chance of having a sub assembly
            'sub_assembly_name' => $this->faker->optional(0.5, null)->sentence(2),
            'step' => $this->faker->sentence(4),
            'machine_id' => $this->faker->optional(0.7, null)->uuid, // 70% chance of having a machine
            'target_qty' => $this->faker->numberBetween(1, 1000),
            'daily_target' => $this->faker->optional(0.6, null)->numberBetween(1, 100),
            'completed_qty' => $this->faker->numberBetween(0, 500),
            'defect_qty' => $this->faker->numberBetween(0, 50),
            'status' => $this->faker->randomElement(['PENDING', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'DOWNTIME']),
            'note' => $this->faker->optional(0.4, null)->paragraph,
            'total_downtime_minutes' => $this->faker->numberBetween(0, 480), // Max 8 hours in minutes
        ];
    }
}
