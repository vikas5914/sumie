<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Manga;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MangaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mangas = [
            [
                'title' => 'One Piece',
                'author' => 'Eiichiro Oda',
                'artist' => 'Eiichiro Oda',
                'description' => 'Gold Roger was known as the "Pirate King", the strongest and most infamous being to have sailed the Grand Line. The capture and execution of Roger by the World Government brought a change to the entire world. His last words before his death revealed the existence of the greatest treasure in the world, One Piece.',
                'cover_image_url' => 'https://mangadex.org/covers/f9c33607-9180-4ba6-b85c-e4b5faee7192/f041f7c6-64eb-431c-a759-3e636e330da0.jpg',
                'status' => 'ongoing',
                'genres' => ['Action', 'Adventure', 'Comedy', 'Fantasy', 'Shounen'],
                'themes' => ['Supernatural', 'Pirates'],
                'demographics' => 'Shounen',
                'total_chapters' => 1090,
                'release_year' => 1997,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.9,
                'rating_count' => 2400,
                'view_count' => 2400000,
                'is_featured' => true,
            ],
            [
                'title' => 'Chainsaw Man',
                'author' => 'Tatsuki Fujimoto',
                'artist' => 'Tatsuki Fujimoto',
                'description' => 'Denji has a simple dream—to live a happy, peaceful life, spending time with girls he likes. He\'s stuck in the yakuza, dying to pay off his deceased father\'s debt. But when his debt is finally paid off, the yakuza decide to kill him and harvest his organs. With the help of his pet devil Pochita, he becomes the Chainsaw Man.',
                'cover_image_url' => 'https://mangadex.org/covers/a77742b1-befd-49a4-b6b4-7b47da68df34/1c1a87fd-2d94-4482-8427-450ba424a254.jpg',
                'status' => 'ongoing',
                'genres' => ['Action', 'Comedy', 'Drama', 'Horror', 'Shounen', 'Supernatural'],
                'themes' => ['Gore', 'Demons', 'Police'],
                'demographics' => 'Shounen',
                'total_chapters' => 143,
                'release_year' => 2018,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.8,
                'rating_count' => 1850,
                'view_count' => 1500000,
                'is_featured' => true,
            ],
            [
                'title' => 'Jujutsu Kaisen',
                'author' => 'Gege Akutami',
                'artist' => 'Gege Akutami',
                'description' => 'Yuuji is a genius at track and field. But he has zero interest running around in circles, he\'s happy as a clam in the Occult Research Club. Although he\'s only in the club for kicks, things get serious when a real spirit shows up at school! Life\'s about to get really strange in Sugisawa Town #3 High School!',
                'cover_image_url' => 'https://mangadex.org/covers/c52b2ce3-7f95-469c-96b0-479524fb7a1a/e8d941a2-8ce5-4541-88a8-f71f0f33d4b8.jpg',
                'status' => 'completed',
                'genres' => ['Action', 'Comedy', 'Drama', 'Horror', 'Shounen', 'Supernatural'],
                'themes' => ['Martial Arts', 'School Life', 'Gore'],
                'demographics' => 'Shounen',
                'total_chapters' => 236,
                'release_year' => 2017,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.7,
                'rating_count' => 2200,
                'view_count' => 1800000,
                'is_featured' => true,
            ],
            [
                'title' => 'Blue Lock',
                'author' => 'Muneyuki Kaneshiro',
                'artist' => 'Yusuke Nomura',
                'description' => 'After reflecting on the current state of Japanese soccer, the Japanese Football Association decides to hire the enigmatic and eccentric coach Jinpachi Ego to achieve their dream of winning the World Cup. Ego\'s plan is to take 300 U-18 strikers and put them through a rigorous training program to find the one true egoistic striker.',
                'cover_image_url' => 'https://mangadex.org/covers/ce7cd750-a84f-47f2-95f3-5b8a523cc1f3/a39f6fa8-7f5a-47ca-81de-45e13599618f.jpg',
                'status' => 'ongoing',
                'genres' => ['Action', 'Drama', 'Sports'],
                'themes' => ['Psychological', 'Competitive Sports', 'Team Sports'],
                'demographics' => 'Shounen',
                'total_chapters' => 245,
                'release_year' => 2018,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.6,
                'rating_count' => 980,
                'view_count' => 850000,
                'is_featured' => false,
            ],
            [
                'title' => 'Spy x Family',
                'author' => 'Tatsuya Endo',
                'artist' => 'Tatsuya Endo',
                'description' => 'The master spy codenamed <Twilight> has spent his days on undercover missions, all for the dream of a better world. But one day, he receives a particularly difficult new order from command. For his mission, he must form a temporary family and start a new life?!',
                'cover_image_url' => 'https://mangadex.org/covers/6b9586c4-83b5-4638-a0cc-2bfaad1f3e5d/4f7a8e90-a7c0-445c-a6c5-9e6d7b5f6fc7.jpg',
                'status' => 'ongoing',
                'genres' => ['Action', 'Comedy', 'Slice of Life', 'Shounen'],
                'themes' => ['Supernatural', 'School Life', 'Espionage'],
                'demographics' => 'Shounen',
                'total_chapters' => 87,
                'release_year' => 2019,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.8,
                'rating_count' => 1560,
                'view_count' => 1200000,
                'is_featured' => true,
            ],
            [
                'title' => 'My Hero Academia',
                'author' => 'Kohei Horikoshi',
                'artist' => 'Kohei Horikoshi',
                'description' => 'One day, a four-year-old boy came to a sudden realization: the world is not fair. Eighty percent of the world\'s population wield special abilities, known as "quirks," which have given many the power to make their childhood dreams of becoming a superhero a reality.',
                'cover_image_url' => 'https://mangadex.org/covers/4f3bcae4-2d72-4243-b99a-77c461121c56/4cc2c9d8-fbf2-49d9-82a0-7fc3d4d39e1a.jpg',
                'status' => 'completed',
                'genres' => ['Action', 'Comedy', 'Drama', 'Shounen', 'Supernatural'],
                'themes' => ['School Life', 'Superhero', 'Super Power'],
                'demographics' => 'Shounen',
                'total_chapters' => 430,
                'release_year' => 2014,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.5,
                'rating_count' => 2100,
                'view_count' => 1600000,
                'is_featured' => false,
            ],
            [
                'title' => 'Attack on Titan',
                'author' => 'Hajime Isayama',
                'artist' => 'Hajime Isayama',
                'description' => 'Several hundred years ago, humans were nearly exterminated by titans. Titans are typically several stories tall, seem to have no intelligence, devour human beings and, worst of all, seem to do it for the pleasure rather than as a food source.',
                'cover_image_url' => 'https://mangadex.org/covers/304ceac3-8cdb-4fe7-acf7-2b6ff9a60693/0f02d5a3-4b05-4d47-9e7a-4e96c76b2d2e.jpg',
                'status' => 'completed',
                'genres' => ['Action', 'Drama', 'Fantasy', 'Horror', 'Mystery', 'Shounen', 'Supernatural', 'Tragedy'],
                'themes' => ['Military', 'Survival', 'Gore', 'Dark Fantasy'],
                'demographics' => 'Shounen',
                'total_chapters' => 139,
                'release_year' => 2009,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.9,
                'rating_count' => 2800,
                'view_count' => 2100000,
                'is_featured' => true,
            ],
            [
                'title' => 'Demon Slayer',
                'author' => 'Koyoharu Gotouge',
                'artist' => 'Koyoharu Gotouge',
                'description' => 'Since ancient times, rumors have abounded of man-eating demons lurking in the woods. Because of this, the local townsfolk never venture outside at night. Legend has it that a Demon Slayer also roams the night, hunting down these bloodthirsty demons.',
                'cover_image_url' => 'https://mangadex.org/covers/789642f8-ca89-4f23-a861-12b429f15f11/0b5f4a56-1e42-45e8-85f8-1c3e4b2c13a7.jpg',
                'status' => 'completed',
                'genres' => ['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Historical', 'Shounen', 'Supernatural'],
                'themes' => ['Demons', 'Martial Arts', 'Super Power'],
                'demographics' => 'Shounen',
                'total_chapters' => 205,
                'release_year' => 2016,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.7,
                'rating_count' => 1900,
                'view_count' => 1700000,
                'is_featured' => true,
            ],
            [
                'title' => 'Vinland Saga',
                'author' => 'Makoto Yukimura',
                'artist' => 'Makoto Yukimura',
                'description' => 'Thorfinn is the son of one of the Viking\'s greatest warriors, Thors. When Thorfinn was a child, he longed for the sea and to join his father on his adventures. But when he finally gets his wish, it\'s not the life he imagined.',
                'cover_image_url' => 'https://mangadex.org/covers/6448e5d5-0e65-4c93-a03e-2c8b871e9e49/0b5f4a56-1e42-45e8-85f8-1c3e4b2c13a7.jpg',
                'status' => 'ongoing',
                'genres' => ['Action', 'Adventure', 'Drama', 'Historical', 'Seinen'],
                'themes' => ['Military', 'Psychological', 'Revenge', 'Vikings'],
                'demographics' => 'Seinen',
                'total_chapters' => 198,
                'release_year' => 2005,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.8,
                'rating_count' => 1100,
                'view_count' => 650000,
                'is_featured' => false,
            ],
            [
                'title' => 'Berserk',
                'author' => 'Kentaro Miura',
                'artist' => 'Kentaro Miura',
                'description' => 'Guts, a former mercenary now known as the "Black Swordsman," is out for revenge. After a tumultuous childhood, he finally finds someone he respects and believes he can trust, only to have everything fall apart when this person takes away everything important to Guts.',
                'cover_image_url' => 'https://mangadex.org/covers/c9c1a9a9-73fd-4be3-81af-1fc9a88968d8/0b5f4a56-1e42-45e8-85f8-1c3e4b2c13a7.jpg',
                'status' => 'hiatus',
                'genres' => ['Action', 'Adventure', 'Drama', 'Fantasy', 'Horror', 'Seinen', 'Supernatural', 'Tragedy'],
                'themes' => ['Demons', 'Military', 'Gore', 'Psychological', 'Dark Fantasy'],
                'demographics' => 'Seinen',
                'total_chapters' => 364,
                'release_year' => 1989,
                'country_of_origin' => 'Japan',
                'rating_average' => 4.9,
                'rating_count' => 1700,
                'view_count' => 900000,
                'is_featured' => true,
            ],
        ];

        foreach ($mangas as $mangaData) {
            $slug = Str::slug($mangaData['title']);
            $mangaData['slug'] = $slug;
            $mangaData['published_at'] = Carbon::now()->subYears(rand(1, 30));

            $manga = Manga::create($mangaData);

            // Create sample chapters for each manga
            $this->createChapters($manga);
        }
    }

    private function createChapters(Manga $manga): void
    {
        $chapters = min($manga->total_chapters, 10); // Create up to 10 chapters

        for ($i = 1; $i <= $chapters; $i++) {
            Chapter::create([
                'manga_id' => $manga->id,
                'chapter_number' => $i,
                'volume_number' => ceil($i / 10),
                'title' => "Chapter {$i}",
                'page_count' => rand(18, 25),
                'release_date' => Carbon::now()->subDays(rand(1, 365)),
                'is_published' => true,
            ]);
        }
    }
}
