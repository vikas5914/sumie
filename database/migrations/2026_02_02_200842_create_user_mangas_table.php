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
        Schema::create('user_mangas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manga_id')->constrained('mangas')->onDelete('cascade');
            $table->enum('status', ['reading', 'completed', 'on_hold', 'dropped', 'planned'])->default('reading');
            $table->foreignId('current_chapter_id')->nullable()->constrained('chapters')->onDelete('set null');
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->tinyInteger('rating')->nullable()->unsigned();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('notify_on_update')->default(true);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'manga_id']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'is_favorite']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mangas');
    }
};
