<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_business_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id(); // Solo habrá una fila para la configuración global
            $table->integer('opening_hour')->default(9);
            $table->integer('closing_hour')->default(19);
            $table->integer('lunch_start_hour')->nullable();
            $table->integer('lunch_end_hour')->nullable();
            $table->json('disabled_dates')->nullable(); // Array de strings 'YYYY-MM-DD'
            $table->json('daily_hours')->nullable(); // Configuración específica por día
            $table->timestamps();
        });

        // Insertar una configuración por defecto
        \App\Models\BusinessSetting::create([
            'opening_hour' => 9,
            'closing_hour' => 19,
            'lunch_start_hour' => 14,
            'lunch_end_hour' => 15,
            'disabled_dates' => json_encode([]),
            'daily_hours' => json_encode([]),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('business_settings');
    }
};
