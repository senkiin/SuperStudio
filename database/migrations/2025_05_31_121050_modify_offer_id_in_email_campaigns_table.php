<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // Para registrar información si es necesario

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            // Verificar si la columna 'offer_id' existe antes de intentar modificarla.
            // Esto es una buena práctica, aunque el error ya te dice que existe.
            if (Schema::hasColumn('email_campaigns', 'offer_id')) {
                // Para modificar una columna con clave foránea, a veces es necesario
                // eliminar la restricción primero, cambiar la columna y luego volver a agregarla.
                // El nombre de la restricción puede variar (ej. 'email_campaigns_offer_id_foreign').
                // Intenta encontrarlo en tu esquema de BD o en errores previos si esto falla.
                try {
                    // Intenta eliminar la clave foránea si existe.
                    // El nombre de la restricción es usualmente: nombredetabla_nombredelacolumna_foreign
                    $table->dropForeign(['offer_id']); // O $table->dropForeign('email_campaigns_offer_id_foreign');
                } catch (\Exception $e) {
                    Log::info("Nota: No se pudo eliminar la clave foránea para offer_id en email_campaigns durante la migración (puede que no exista o tenga un nombre diferente): " . $e->getMessage());
                }

                // Ahora modifica la columna para que sea nulable.
                // Asegúrate de que onDelete('set null') sea lo que quieres si la oferta se elimina.
                $table->foreignId('offer_id')->nullable()->change();

                // Vuelve a añadir la restricción de clave foránea si la tabla 'offers' existe.
                // Si la tabla 'offers' no existe, esta parte fallará o no se debe ejecutar.
                if (Schema::hasTable('offers')) {
                    $table->foreign('offer_id')
                          ->references('id')
                          ->on('offers')
                          ->onDelete('set null'); // O 'cascade', o lo que necesites
                }
            } else {
                // Si por alguna razón la columna no existiera (contrario al error actual), la crearías.
                // Esto es más para una lógica defensiva.
                // $table->foreignId('offer_id')->nullable()->constrained('offers')->onDelete('set null');
                Log::warning("La columna 'offer_id' no se encontró en la tabla 'email_campaigns' durante la migración de modificación, lo cual es inesperado dado el error previo.");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            // Lógica para revertir: hacerla no nulable de nuevo (esto podría fallar si hay NULLs)
            // De nuevo, manejar la clave foránea
            if (Schema::hasColumn('email_campaigns', 'offer_id')) {
                try {
                     $table->dropForeign(['offer_id']);
                } catch (\Exception $e) {
                     Log::info("Nota al revertir: No se pudo eliminar la clave foránea para offer_id: " . $e->getMessage());
                }

                // Asumiendo que quieres volver a 'cascade' y no nulable
                $table->foreignId('offer_id')->nullable(false)->change(); // Quitar nullable()

                if (Schema::hasTable('offers')) {
                     $table->foreign('offer_id')
                           ->references('id')
                           ->on('offers')
                           ->onDelete('cascade'); // O el onDelete original
                }
            }
        });
    }
};
