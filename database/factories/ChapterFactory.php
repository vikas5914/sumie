<?php

namespace Database\Factories;

use App\Models\Manga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chapterId = fake()->unique()->regexify('[a-z0-9]{10}');
        $chapterNumber = (string) fake()->randomFloat(1, 1, 300);

        return [
            'id' => $chapterId,
            'manga_id' => Manga::factory(),
            'chapter_number' => $chapterNumber,
            'volume' => (string) fake()->numberBetween(1, 40),
            'title' => fake()->sentence(4),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'language' => 'en',
            'node' => 'https://s13.weebdex.net',
            'pages' => [
                [
                    'name' => '1-sample.jpg',
                    'dimensions' => [800, 1200],
                ],
            ],
            'page_count' => fake()->numberBetween(10, 45),
            'is_unavailable' => false,
            'source_payload' => [
                'id' => $chapterId,
                'chapter' => $chapterNumber,
            ],
            'synced_at' => now(),
        ];
    }
}
