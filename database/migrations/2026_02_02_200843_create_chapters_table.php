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
            $table->id();
            $table->string('manga_id', 191);
            $table->decimal('chapter_number', 10, 2)->default(0);
            $table->string('chapter_label')->nullable();
            $table->string('volume_number')->nullable();
            $table->string('title')->nullable();
            $table->integer('page_count')->default(0);
            $table->timestamp('release_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('language', 12)->default('en');
            $table->string('source_url')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();

            $table->foreign('manga_id')->references('id')->on('mangas')->onDelete('cascade');
            $table->unique(['manga_id', 'external_id']);
            $table->index(['manga_id', 'chapter_number']);
            $table->index(['manga_id', 'release_date']);
            $table->index('external_id');
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
