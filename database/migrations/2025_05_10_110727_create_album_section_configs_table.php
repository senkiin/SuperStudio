<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('album_section_configs', function (Blueprint $table) {
            $table->id();
            // Identificador único para esta sección específica, si planeas tener múltiples.
            // Si solo es una, puedes omitirlo y siempre buscar por id=1.
            $table->string('identifier')->unique()->default('default_featured_albums');
            $table->string('section_title')->nullable(); // Ej: "2025", "Featured Trips"
            $table->json('selected_album_ids_ordered')->nullable(); // Guarda un array de IDs de álbumes en el orden deseado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_section_configs');
    }
};
