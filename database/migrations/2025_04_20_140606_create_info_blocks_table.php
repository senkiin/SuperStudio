<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_info_blocks_table.php

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
        Schema::create('info_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->string('link_text')->default('Saber Más');
            $table->enum('image_position', ['left', 'right'])->default('left');
            $table->integer('order_column')->default(0); // Para ordenar los bloques
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_blocks');
    }
};
