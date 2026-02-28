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
        Schema::create('chapters', function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('manga_id', 64);
            $table->string('chapter_number', 32)->nullable();
            $table->string('volume', 32)->nullable();
            $table->string('title')->nullable();
            $table->string('language', 16)->default('en');
            $table->timestamp('published_at')->nullable();
            $table->string('node')->nullable();
            $table->json('pages')->nullable();
            $table->unsignedInteger('page_count')->default(0);
            $table->boolean('is_unavailable')->default(false);
            $table->json('source_payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->foreign('manga_id')->references('id')->on('mangas')->onDelete('cascade');
            $table->index(['manga_id', 'chapter_number']);
            $table->index(['manga_id', 'published_at']);
            $table->index(['language', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
