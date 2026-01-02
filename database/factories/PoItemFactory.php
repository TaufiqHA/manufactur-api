<?php

namespace Database\Factories;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PoItem>
 */
class PoItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'po_id' => PurchaseOrder::factory(),
            'material_id' => Material::factory(),
            'name' => $this->faker->word(),
            'qty' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 10, 10000),
        ];
    }
}
