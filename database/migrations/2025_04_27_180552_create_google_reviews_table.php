<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_reviews', function (Blueprint $table) {
            $table->id();
            // --- MODIFICACIÓN AQUÍ ---
            $table->string('author_name')->nullable(); // Permitir nulos temporalmente
            // --- FIN MODIFICACIÓN ---
            $table->string('author_url')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('profile_photo_url')->nullable();
            $table->unsignedTinyInteger('rating'); // Rating sí es obligatorio
            $table->string('relative_time_description')->nullable();
            $table->text('text')->nullable();
            // --- MODIFICACIÓN AQUÍ (opcional, pero consistente si puede faltar) ---
            $table->timestamp('review_time')->nullable(); // Permitir nulos si time pudiera faltar
            // --- FIN MODIFICACIÓN ---
            $table->boolean('translated')->default(false);
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();

            // Quitar el índice unique si author_name o review_time ahora son nullable
            // $table->unique(['author_name', 'review_time']); // Comentar o eliminar si permites nulos

            $table->index('rating');
            $table->index('review_time');
            $table->index('is_visible'); // Asegúrate que este índice esté aquí (parecía faltar antes)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_reviews');
    }
};
