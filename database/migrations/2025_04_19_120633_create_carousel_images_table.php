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
        Schema::create('carousel_images', function (Blueprint $table) {
            $table->id();
            // Guarda la ruta relativa dentro del disco de storage (ej: 'carousel_images/imagen_xyz.jpg')
            $table->string('image_path');
            // Ruta a la miniatura (opcional pero recomendado para admin)
            $table->string('thumbnail_path')->nullable();
            // Orden de aparición en el carrusel
            $table->unsignedInteger('order')->default(0);
            // Título/Caption opcional para mostrar sobre la imagen
            $table->string('caption')->nullable();
            // Enlace opcional al hacer clic en la imagen
            $table->string('link_url')->nullable();
            // Para saber si está activa o no en el carrusel
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_images');
    }
};

