<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReceivingGood>
 */
class ReceivingGoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'RG-' . strtoupper(Str::random(8)),
            'date' => $this->faker->date(),
            'po_id' => \App\Models\PurchaseOrder::factory(), // This will create a related PurchaseOrder if one doesn't exist
        ];
    }
}
