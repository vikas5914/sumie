<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manga>
 */
class MangaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'author' => fake()->name(),
            'artist' => fake()->name(),
            'description' => fake()->paragraph(),
            'cover_image_url' => 'https://via.placeholder.com/300x450/1a1a1a/4CFF00?text='.urlencode($title),
            'status' => fake()->randomElement(['ongoing', 'completed', 'hiatus']),
            'genres' => fake()->randomElements(['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror', 'Romance', 'Sci-Fi'], 3),
            'total_chapters' => fake()->numberBetween(10, 200),
            'rating_average' => fake()->randomFloat(2, 3, 5),
            'rating_count' => fake()->numberBetween(100, 5000),
            'view_count' => fake()->numberBetween(1000, 100000),
        ];
    }
}
