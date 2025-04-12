<?php

namespace Database\Factories;

use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
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
            'service_type_id'=>ServiceType::all()->random()->id,
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'canceled']), 
            'notes'=>fake()->paragraph(2),
            'appointment_date'=>fake()->date,
        ];
    }
}
