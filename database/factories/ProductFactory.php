<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        fake()->addProvider(new \Mmo\Faker\PicsumProvider(fake()));
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(2),
            'price' => fake()->randomFloat(2, 5.00, 10000.00),
            'category_id' => null,
            'representative_image'=>'images/products/'.fake()->picsum('public/storage/images/products', 400, 400, false),
            'type_product'=>fake()->randomElement(['physical','digital','photo','poster']),
        ];
    }
}
