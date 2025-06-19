<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'blog_post_id']); // Un usuario solo puede dar un "like" por post.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_likes');
    }
};
