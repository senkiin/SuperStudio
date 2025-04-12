<?php

namespace Database\Factories;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
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
            'name'=>fake()->words(3, true),
            'description'=>fake()->paragraph(2),
            'cover_image'=> 'images/albums/'.fake()->picsum('public/storage/images/albums', 400, 400, false),
            'user_id'=>User::all()->random()->id,
            'type'=> fake()->randomElement(['private','client','public']),
            'client_id'=>User::all()->random()->id,

        ];
    }
}
