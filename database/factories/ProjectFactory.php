<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('PROJ-?????'),
            'name' => $this->faker->sentence(3),
            'customer' => $this->faker->company(),
            'start_date' => $this->faker->date(),
            'deadline' => $this->faker->date(),
            'status' => $this->faker->randomElement(['PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD']),
            'progress' => $this->faker->numberBetween(0, 100),
            'qty_per_unit' => $this->faker->numberBetween(1, 100),
            'procurement_qty' => $this->faker->numberBetween(1, 1000),
            'total_qty' => $this->faker->numberBetween(1, 10000),
            'unit' => $this->faker->randomElement(['unit', 'pcs', 'set', 'box', 'kg', 'm', 'm2', 'm3']),
            'is_locked' => $this->faker->boolean(),
        ];
    }
}
