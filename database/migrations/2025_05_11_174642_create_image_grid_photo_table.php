<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_image_grid_photo_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_grid_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_grid_id')->constrained('image_grids')->onDelete('cascade');
            $table->foreignId('photo_id')->constrained('photos')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['image_grid_id', 'photo_id']); // Evitar duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_grid_photo');
    }
};
