<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mangas', function (Blueprint $table) {
            // Fix: latest_uploaded_chapter should be string (UUID), not datetime
            $table->string('latest_uploaded_chapter_uuid')->nullable()->after('latest_uploaded_chapter');

            // Fix: demographics should be json/text, not varchar
            $table->text('demographics_data')->nullable()->after('demographics');

            // Add missing fields from MangaDex API
            $table->json('alt_titles')->nullable()->after('title');
            $table->string('original_language', 10)->nullable()->after('country_of_origin');
            $table->string('last_volume')->nullable()->after('total_chapters');
            $table->string('last_chapter')->nullable()->after('last_volume');
            $table->json('links')->nullable()->after('source_url');
            $table->json('available_translated_languages')->nullable()->after('original_language');
            $table->boolean('is_locked')->default(false)->after('is_licensed');
            $table->integer('api_version')->default(1)->after('external_id');
            $table->timestamp('created_at_api')->nullable()->after('last_fetched_at');
            $table->timestamp('updated_at_api')->nullable()->after('created_at_api');
            $table->boolean('chapter_numbers_reset_on_new_volume')->default(false)->after('api_version');
            $table->string('state', 20)->default('published')->after('chapter_numbers_reset_on_new_volume');

            // Additional tag groups we weren't capturing
            $table->json('content_tags')->nullable()->after('themes');
            $table->json('format_tags')->nullable()->after('content_tags');

            // Indexes for new fields
            $table->index('original_language');
            $table->index('last_chapter');
            $table->index('updated_at_api');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mangas', function (Blueprint $table) {
            $table->dropColumn([
                'latest_uploaded_chapter_uuid',
                'demographics_data',
                'alt_titles',
                'original_language',
                'last_volume',
                'last_chapter',
                'links',
                'available_translated_languages',
                'is_locked',
                'api_version',
                'created_at_api',
                'updated_at_api',
                'chapter_numbers_reset_on_new_volume',
                'state',
                'content_tags',
                'format_tags',
            ]);

            $table->dropIndex(['original_language']);
            $table->dropIndex(['last_chapter']);
            $table->dropIndex(['updated_at_api']);
        });
    }
};
