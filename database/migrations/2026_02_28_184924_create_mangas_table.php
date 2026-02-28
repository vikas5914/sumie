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
            $table->string('id', 64)->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('unknown');
            $table->string('demographic')->nullable();
            $table->string('content_rating')->default('safe');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('language', 16)->nullable();
            $table->string('cover_id', 64)->nullable();
            $table->string('cover_ext', 16)->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('genres')->nullable();
            $table->json('themes')->nullable();
            $table->json('authors')->nullable();
            $table->json('artists')->nullable();
            $table->json('available_languages')->nullable();
            $table->json('links')->nullable();
            $table->unsignedInteger('chapters_count')->default(0);
            $table->unsignedInteger('follows_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->json('source_payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('status');
            $table->index('content_rating');
            $table->index('year');
            $table->index('synced_at');
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
