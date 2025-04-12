<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(2),
            'start_date' => fake()->date,
            'end_date' => fake()->date,
            'discount_percentage'=>fake()->randomFloat(2, 5, 100),
            'type'=>fake()->randomElement(['percentage','fixed']),
        ];
    }
}
