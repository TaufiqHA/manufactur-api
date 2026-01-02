<?php

namespace Database\Factories;

use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BomItem>
 */
class BomItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get existing IDs or create them if they don't exist
        $projectItemId = ProjectItem::inRandomOrder()->first()?->id ?? ProjectItem::factory()->create()->id;
        $materialId = Material::inRandomOrder()->first()?->id ?? Material::factory()->create()->id;

        return [
            'item_id' => $projectItemId,
            'material_id' => $materialId,
            'quantity_per_unit' => $this->faker->numberBetween(1, 100),
            'total_required' => $this->faker->numberBetween(1, 500),
            'allocated' => $this->faker->numberBetween(0, 500),
            'realized' => $this->faker->numberBetween(0, 500),
        ];
    }
}
