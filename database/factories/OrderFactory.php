<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>User::all()->random()->id,
            'total_amount' => fake()->randomFloat(2, 10.00, 50000.00),
            'status'=>fake()->randomElement(['pending', 'completed', 'cancelled', 'failed', 'in_proces_to_pay','sent']),
        ];
    }
}
