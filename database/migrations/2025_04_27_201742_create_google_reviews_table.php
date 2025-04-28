<?php
// Archivo: database/migrations/2025_04_27_180552_create_google_reviews_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author_name'); // Obligatorio según el último error
            $table->string('author_url')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('relative_time_description')->nullable();
            $table->text('text')->nullable();
            $table->timestamp('review_time'); // Obligatorio según el último error
            $table->boolean('translated')->default(false);
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();

            $table->unique(['author_name', 'review_time']);
            $table->index('rating');
            $table->index('review_time');
            // $table->index('is_visible'); // Ya añadido arriba con ->index()
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_reviews');
    }
};
