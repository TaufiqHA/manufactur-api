<?php

namespace Database\Factories;

use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfigs;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
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
            'sub_assembly_id' => $this->faker->boolean(70) ? SubAssembly::factory() : null, // 70% chance of having a sub assembly
            'source_step_id' => $this->faker->boolean(50) ? ItemStepConfigs::factory() : null, // 50% chance of having a source step
            'target_step_id' => ItemStepConfigs::factory(),
            'task_id' => $this->faker->boolean(70) ? Task::factory() : null, // 70% chance of having a task
            'created_by' => $this->faker->boolean(80) ? User::factory() : null, // 80% chance of having a creator
            'quantity' => function (array $attributes) {
                return $this->faker->numberBetween(1, 100);
            },
            'good_qty' => function (array $attributes) {
                $quantity = $attributes['quantity'] ?? $this->faker->numberBetween(1, 100);
                return $this->faker->numberBetween(0, $quantity);
            },
            'defect_qty' => function (array $attributes) {
                $quantity = $attributes['quantity'] ?? 10; // Default to 10 if not set
                $goodQty = $attributes['good_qty'] ?? $this->faker->numberBetween(0, $quantity);
                return $quantity - $goodQty; // Ensure the sum equals quantity
            },
            'movement_type' => $this->faker->randomElement(['PRODUCTION', 'CONSUMPTION', 'ADJUSTMENT']),
            'shift' => $this->faker->randomElement(['SHIFT_1', 'SHIFT_2', 'SHIFT_3']),
        ];
    }
}
