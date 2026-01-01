<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('MAT-?????'),
            'name' => $this->faker->word . ' ' . $this->faker->word,
            'unit' => $this->faker->randomElement(['pcs', 'kg', 'meter', 'liter', 'unit']),
            'current_stock' => $this->faker->numberBetween(0, 1000),
            'safety_stock' => $this->faker->numberBetween(0, 100),
            'price_per_unit' => $this->faker->randomFloat(2, 1, 999999),
            'category' => $this->faker->randomElement(['RAW', 'FINISHING', 'HARDWARE']),
        ];
    }
}
