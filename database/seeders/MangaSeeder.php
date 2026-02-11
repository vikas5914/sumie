<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Manga;
use Illuminate\Database\Seeder;

class MangaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mangas = Manga::factory()->count(10)->create();

        foreach ($mangas as $manga) {
            $chapterCount = min(10, max(3, (int) floor($manga->total_chapters / 30)));

            for ($chapterNumber = 1; $chapterNumber <= $chapterCount; $chapterNumber++) {
                Chapter::factory()->create([
                    'manga_id' => $manga->id,
                    'chapter_number' => $chapterNumber,
                    'chapter_label' => (string) $chapterNumber,
                    'external_id' => 'seed-'.$manga->id.'-'.$chapterNumber,
                ]);
            }
        }
    }
}
