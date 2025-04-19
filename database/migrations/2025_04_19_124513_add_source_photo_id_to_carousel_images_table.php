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
        Schema::table('carousel_images', function (Blueprint $table) {
            // Añade la columna para referenciar la foto original (si aplica)
            // Debe ser nullable porque las imágenes subidas directamente no tendrán origen.
            // Asegúrate que 'photos' sea el nombre correcto de tu tabla de fotos.
            $table->foreignId('photo_id')
                  ->nullable() // Importante: permite valores nulos
                  ->after('id') // O donde prefieras colocarla
                  ->constrained('photos') // Asume que la tabla se llama 'photos' y la PK es 'id'
                  ->onDelete('set null'); // Opcional: si borras la Photo original, pone null aquí
                                          // Considera 'cascade' si quieres que se borre la entrada del carrusel también,
                                          // o nada ('restrict' por defecto) si quieres prevenir el borrado de Photo si está en el carrusel.
                                          // 'set null' parece lo más seguro aquí.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carousel_images', function (Blueprint $table) {
            // Eliminar la foreign key constraint ANTES de eliminar la columna
            // El nombre de la constraint suele ser 'nombTabla_nombColumna_foreign'
            $table->dropForeign(['photo_id']); // Ajusta el nombre si es diferente
            $table->dropColumn('photo_id');
        });
    }
};
