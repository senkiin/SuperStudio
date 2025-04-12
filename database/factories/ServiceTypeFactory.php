<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceType>
 */
class ServiceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(2),
            // --- CORRECCIÓN AQUÍ ---
            'duration' => fake()->numberBetween(30, 180), // Ejemplo: duración entre 30 y 180 minutos
            'price' => fake()->randomFloat(2, 50, 1000), // Ejemplo: precio entre 50 y 1000
        ];
    }
}
