<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curated_gallery_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curated_gallery_id')->constrained('curated_galleries')->onDelete('cascade');
            $table->foreignId('photo_id')->constrained('photos')->onDelete('cascade');
            $table->integer('order')->default(0); // Para el orden de las fotos en la galería
            $table->timestamps();
            $table->unique(['curated_gallery_id', 'photo_id']); // Evitar duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curated_gallery_photo');
    }
};
