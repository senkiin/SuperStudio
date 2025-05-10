<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_section_settings', function (Blueprint $table) { // Nuevo nombre de tabla
            $table->id();
            $table->string('hero_title');
            $table->text('hero_subtitle');
            $table->string('background_image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_section_settings'); // Nuevo nombre de tabla
    }
};
