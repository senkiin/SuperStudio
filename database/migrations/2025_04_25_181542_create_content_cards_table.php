<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_content_cards_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable(); // Description might be optional
            $table->string('image_path'); // Assuming image is required per card
            $table->string('link_url')->nullable();
            $table->string('link_text')->default('Saber Más');
            $table->integer('order_column')->default(0); // For drag-and-drop ordering
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_cards');
    }
};
