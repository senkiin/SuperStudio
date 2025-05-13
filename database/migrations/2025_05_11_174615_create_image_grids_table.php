<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_image_grids_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_grids', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // Ej: 'home_portraits', 'team_gallery'
            $table->string('title')->nullable();   // Un título opcional para mostrar sobre el grid
            $table->text('description')->nullable(); // Una descripción opcional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_grids');
    }
};
