<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            // Cambiar la columna para permitir NULLs
            $table->foreignId('album_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            // Revertir si es necesario (esto podría fallar si hay NULLs)
            $table->foreignId('album_id')->nullable(false)->change();
        });
    }
};
