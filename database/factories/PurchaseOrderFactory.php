<?php

namespace Database\Factories;

use App\Models\Rfq;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('PO-#####'),
            'date' => $this->faker->date(),
            'supplier_id' => Supplier::factory(),
            'rfq_id' => Rfq::factory(),
            'description' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['OPEN', 'RECEIVED']),
            'grand_total' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
