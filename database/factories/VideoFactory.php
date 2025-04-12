<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'album_id' => Album::all()->random()->id,
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(2),
            'thumbnail_path'=>null,
            'duration'=>fake()->numberBetween(30, 600),
            'resolution'=>null,
            'uploaded_by'=>User::all()->random()->id,
            'file_path'=>'public/storage/videos/'.fake()->slug() . '.mp4'
        ];
    }
}
