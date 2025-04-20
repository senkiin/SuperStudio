<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verifica si la tabla 'carousel_images' existe antes de intentar modificarla
        if (Schema::hasTable('carousel_images')) {
            Schema::table('carousel_images', function (Blueprint $table) {

                // --- Añade la columna para referenciar la foto original ---
                // (Esto ya estaba en tu archivo original)
                // Solo añade si la columna NO existe ya
                if (!Schema::hasColumn('carousel_images', 'photo_id')) {
                    $table->foreignId('photo_id')
                        ->nullable() // Permite valores nulos
                        ->after('id') // O después de otra columna si prefieres
                        ->constrained('photos') // Asume tabla 'photos', PK 'id'
                        ->onDelete('set null'); // Si se borra Photo original, pone null aquí
                }

                // --- Añade la columna para el texto del enlace ---
                // Solo añade si la columna NO existe ya
                if (!Schema::hasColumn('carousel_images', 'link_text')) {
                     $table->string('link_text')
                           ->nullable() // Permite que sea opcional
                           ->after('link_url'); // Colocar después de la URL del enlace (que ya debe existir)
                }

                /* --- Columnas que YA DEBERÍAN EXISTIR desde la migración de creación ---
                   (Descomenta y ajusta SOLO si estás seguro de que faltan)

                // Título/Caption (Ya debería existir)
                if (!Schema::hasColumn('carousel_images', 'caption')) {
                    $table->string('caption')->nullable()->after('thumbnail_path'); // O donde corresponda
                }

                // Enlace URL (Ya debería existir)
                 if (!Schema::hasColumn('carousel_images', 'link_url')) {
                     $table->string('link_url')->nullable()->after('caption'); // O donde corresponda
                 }
                */

            });
        } else {
            // Opcional: Log o mensaje si la tabla no existe
             Log::warning("La tabla 'carousel_images' no existe. Omitiendo migración add_source_photo_id.");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verifica si la tabla existe antes de intentar revertir
         if (Schema::hasTable('carousel_images')) {
            Schema::table('carousel_images', function (Blueprint $table) {

                // Eliminar 'link_text' si existe
                if (Schema::hasColumn('carousel_images', 'link_text')) {
                    $table->dropColumn('link_text');
                }

                // Eliminar 'photo_id' si existe (incluyendo la foreign key)
                if (Schema::hasColumn('carousel_images', 'photo_id')) {
                    // Intentar obtener el nombre de la constraint (puede variar)
                     // Comúnmente es 'tabla_columna_foreign'
                     $foreignKeys = collect(Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('carousel_images'));
                     $photoIdFk = $foreignKeys->first(function ($fk) {
                         return in_array('photo_id', $fk->getLocalColumns());
                     });

                     if ($photoIdFk) {
                         $table->dropForeign($photoIdFk->getName());
                     } elseif (Schema::getConnection()->getDriverName() !== 'sqlite') {
                         // Fallback para intentar el nombre común si no se encontró con Doctrine (no funciona en sqlite)
                         try { $table->dropForeign(['photo_id']); } catch (\Exception $e) { Log::warning("No se pudo eliminar la FK 'carousel_images_photo_id_foreign' automáticamente."); }
                     }

                    $table->dropColumn('photo_id');
                }

                 /* --- Revertir columnas que no deberían estar aquí ---
                 // (Descomenta SOLO si las descomentaste en el método up)

                 if (Schema::hasColumn('carousel_images', 'link_url')) {
                     $table->dropColumn('link_url');
                 }
                  if (Schema::hasColumn('carousel_images', 'caption')) {
                      $table->dropColumn('caption');
                  }
                 */
            });
        }
    }
};
