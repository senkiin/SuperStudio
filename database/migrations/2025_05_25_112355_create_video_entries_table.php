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
        Schema::create('video_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_section_id')->constrained('video_sections')->onDelete('cascade');
            $table->string('entry_title'); // Title for the individual video card
            $table->text('entry_description')->nullable(); // Description for the individual video card
            $table->string('video_source_type')->default('vimeo'); // 'vimeo', 's3', 'youtube'
            $table->string('source_identifier'); // Vimeo ID, S3 path, or YouTube ID
            $table->string('thumbnail_url')->nullable(); // Optional: if you want to store a custom thumbnail URL
            $table->integer('order_column')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_entries');
    }
};
