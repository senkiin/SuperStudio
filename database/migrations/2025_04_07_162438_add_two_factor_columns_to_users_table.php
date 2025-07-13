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
        Schema::table('users', function (Blueprint $table) {
            // Comprueba si la columna 'two_factor_secret' NO existe antes de añadirla
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')
                    ->after('password')
                    ->nullable();
            }

            // Comprueba si la columna 'two_factor_recovery_codes' NO existe antes de añadirla
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')
                    ->after('two_factor_secret')
                    ->nullable();
            }

            // Comprueba si la columna 'two_factor_confirmed_at' NO existe antes de añadirla
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')
                    ->after('two_factor_recovery_codes')
                    ->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Define las columnas a eliminar
            $columns = [
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ];

            // Comprueba si las columnas existen antes de intentar eliminarlas
            if (Schema::hasColumns('users', $columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};