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
        Schema::create('mangas', function (Blueprint $table) {
            $table->string('id', 191)->primary();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('source_manga_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('banner_image_url')->nullable();
            $table->string('author')->nullable();
            $table->string('artist')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->default('unknown');
            $table->string('content_rating')->default('safe');
            $table->boolean('is_nsfw')->default(false);
            $table->json('genres')->nullable();
            $table->json('themes')->nullable();
            $table->json('demographics')->nullable();
            $table->json('formats')->nullable();
            $table->integer('total_chapters')->default(0);
            $table->integer('release_year')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->decimal('rating_average', 5, 2)->nullable();
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->json('links')->nullable();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamp('created_at_api')->nullable();
            $table->timestamp('updated_at_api')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('source_manga_id');
            $table->index('title');
            $table->index('type');
            $table->index('status');
            $table->index('is_nsfw');
            $table->index('country_of_origin');
            $table->index('last_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangas');
    }
};
