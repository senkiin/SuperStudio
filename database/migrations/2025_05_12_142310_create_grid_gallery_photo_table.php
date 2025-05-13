<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_gallery_photo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grid_gallery_id')->constrained('grid_galleries')->onDelete('cascade');
            $table->foreignId('photo_id')->constrained('photos')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['grid_gallery_id', 'photo_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('grid_gallery_photo');
    }
};
