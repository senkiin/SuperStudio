<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_carousel_slides_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('background_image_path');
            $table->string('text_color')->default('#FFFFFF'); // Ej: #FFFFFF o text-white
            $table->string('text_animation')->nullable(); // Ej: fade-in-up, slide-in-left
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carousel_slides');
    }
};
