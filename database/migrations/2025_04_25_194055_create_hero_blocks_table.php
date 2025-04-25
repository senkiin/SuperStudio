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
        Schema::create('hero_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path'); // Ruta a la imagen de fondo
            $table->string('link_url')->nullable(); // URL del botón/enlace
            $table->string('link_text')->default('Ver Más'); // Texto del botón/enlace
            $table->boolean('is_active')->default(true); // Opcional: para activar/desactivar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_blocks');
    }
};
