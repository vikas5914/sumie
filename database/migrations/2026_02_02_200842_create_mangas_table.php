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
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author')->nullable();
            $table->string('artist')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->string('banner_image_url')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'hiatus', 'cancelled'])->default('ongoing');
            $table->enum('content_rating', ['safe', 'suggestive', 'erotica', 'pornographic'])->default('safe');
            $table->json('genres')->nullable();
            $table->json('themes')->nullable();
            $table->string('demographics')->nullable();
            $table->integer('total_chapters')->default(0);
            $table->integer('release_year')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->decimal('rating_average', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_licensed')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['rating_average', 'rating_count']);
            $table->index('title');
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
