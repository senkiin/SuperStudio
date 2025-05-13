<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_page_header_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_header_settings', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique(); // Para identificar la cabecera específica
            $table->string('hero_title');
            $table->text('hero_subtitle');
            $table->string('background_image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_header_settings');
    }
};
