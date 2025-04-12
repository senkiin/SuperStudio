<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
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
            'album_id'=>Album::all()->random()->id,
            'file_path'=>'images/photos/'.fake()->picsum('public/storage/images/photos', 400, 400, false),
            'uploaded_by'=>User::all()->random()->id,
            'like'=>fake()->boolean(),
        ];
    }
}
