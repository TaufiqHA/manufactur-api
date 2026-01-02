<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // Will be set when creating related to a user
            'code' => $this->faker->unique()->bothify('MCH-?????'),
            'name' => $this->faker->company() . ' ' . $this->faker->word(),
            'type' => $this->faker->randomElement(['CNC', 'LATHE', 'MILLING', 'DRILLING', 'GRINDING', 'LASER', 'WELDING']),
            'capacity_per_hour' => $this->faker->numberBetween(10, 1000),
            'status' => $this->faker->randomElement(['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME']),
            'is_maintenance' => $this->faker->boolean(),
        ];
    }
}
