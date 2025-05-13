<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_section_settings', function (Blueprint $table) {
            $table->string('identifier')->unique()->after('id'); // Añadido 'identifier'
        });
    }

    public function down(): void
    {
        Schema::table('hero_section_settings', function (Blueprint $table) {
            $table->dropColumn('identifier');
        });
    }
};
