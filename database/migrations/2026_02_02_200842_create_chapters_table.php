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
            $table->foreignId('manga_id')->constrained('mangas')->onDelete('cascade');
            $table->decimal('chapter_number', 8, 2);
            $table->integer('volume_number')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('page_count')->default(0);
            $table->timestamp('release_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('source_url')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();

            $table->index(['manga_id', 'chapter_number']);
            $table->index(['manga_id', 'release_date']);
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
