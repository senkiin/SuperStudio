<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id'=>Product::all()->random()->id,
            'quantity'=>fake()->numberBetween(1, 10),
            'unit_price'=>fake()->randomFloat(2),
            'product_name'=>Product::all()->random()->name,
        ];
    }
}
