<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // No se usa directamente para DB::statement
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Nombre de la clave foránea a gestionar.
     * Puede que necesites encontrar el nombre exacto en tu base de datos si no es el estándar.
     */
    protected string $foreignKeyName = 'appointments_user_id_foreign'; // Nombre común de Laravel

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener el nombre exacto de la clave foránea para 'user_id'
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'appointments'
              AND COLUMN_NAME = 'user_id'
              AND REFERENCED_TABLE_NAME = 'users';
        ");

        if (!empty($foreignKeys) && isset($foreignKeys[0]->CONSTRAINT_NAME)) {
            $this->foreignKeyName = $foreignKeys[0]->CONSTRAINT_NAME;
            Log::info("Clave foránea encontrada para user_id: " . $this->foreignKeyName);
            try {
                DB::statement("ALTER TABLE appointments DROP FOREIGN KEY `{$this->foreignKeyName}`;");
                Log::info("Clave foránea `{$this->foreignKeyName}` eliminada.");
            } catch (\Exception $e) {
                Log::error("Error al intentar eliminar la clave foránea `{$this->foreignKeyName}`: " . $e->getMessage());
                // Si falla aquí, es posible que la FK no exista o el nombre sea incorrecto.
                // Podrías decidir si detener la migración o continuar con precaución.
                // Por ahora, continuaremos para intentar modificar la columna.
            }
        } else {
            Log::warning("No se encontró una clave foránea definida para la columna 'user_id' en 'appointments' que referencie a 'users'.");
            // Asignamos el nombre por defecto por si acaso existe pero la consulta anterior no la encontró por alguna razón
            $this->foreignKeyName = 'appointments_user_id_foreign';
        }

        // Modificar la columna user_id para que sea nullable usando SQL nativo
        try {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN `user_id` BIGINT UNSIGNED NULL;");
            Log::info("Columna 'user_id' modificada a NULLABLE.");
        } catch (\Exception $e) {
            Log::error("Error al modificar la columna 'user_id' a NULLABLE: " . $e->getMessage());
            // Es crucial que esto funcione. Si falla, la aplicación seguirá dando error.
            throw $e; // Relanzar la excepción para detener la migración si esto falla.
        }


        // Volver a añadir la clave foránea, ahora con onDelete('set null')
        try {
            DB::statement("
                ALTER TABLE appointments
                ADD CONSTRAINT `{$this->foreignKeyName}`
                FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`)
                ON DELETE SET NULL;
            ");
            Log::info("Clave foránea `{$this->foreignKeyName}` para 'user_id' re-añadida con ON DELETE SET NULL.");
        } catch (\Exception $e) {
            Log::error("Error al re-añadir la clave foránea `{$this->foreignKeyName}`: " . $e->getMessage());
            // Considera qué hacer si esto falla. Puede dejar la tabla sin la FK.
        }

        // Los otros cambios que tenías en tu migración de "última modificación"
        // deberían estar en SU PROPIA migración o en esta si es la que realmente
        // consolida todas las actualizaciones a la tabla 'appointments'.
        // Aquí asumo que esta migración SOLO se enfoca en 'user_id'.
        // Si necesitas añadir las otras columnas (guest_name, email, phone, appointment_datetime)
        // y aún no lo has hecho, deberías añadirlas aquí también usando Schema::table
        // o en una migración separada que se ejecute DESPUÉS de esta.

        // Ejemplo de cómo añadir las otras columnas si no existen:
        if (!Schema::hasColumn('appointments', 'guest_name')) {
             Schema::table('appointments', function ($table) {
                $table->string('guest_name')->nullable()->after('user_id');
            });
        }
        if (!Schema::hasColumn('appointments', 'email')) {
             Schema::table('appointments', function ($table) {
                $table->string('email')->after('guest_name');
            });
        }
        if (!Schema::hasColumn('appointments', 'phone')) {
             Schema::table('appointments', function ($table) {
                $table->string('phone')->nullable()->after('email');
            });
        }
        if (Schema::hasColumn('appointments', 'appointment_date') && !Schema::hasColumn('appointments', 'appointment_datetime')) {
            Schema::table('appointments', function ($table) {
                $table->renameColumn('appointment_date', 'appointment_datetime_old_temp');
            });
             Schema::table('appointments', function ($table) {
                $table->dateTime('appointment_datetime')->after('notes')->nullable();
                // Aquí la lógica para copiar datos si es necesario
                $table->dropColumn('appointment_datetime_old_temp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir esto con SQL nativo sería:
        // 1. Eliminar la FK creada en up().
        // 2. Modificar user_id para que sea NOT NULL.
        // 3. Recrear la FK original con ON DELETE CASCADE.
        // ¡CUIDADO! Esto fallará si hay user_id con valores NULL.
        // Este método down() es complejo y propenso a errores si no se manejan los datos.

        // Por simplicidad, el rollback es más seguro si se hace un migrate:fresh en desarrollo.
        // Si necesitas un rollback funcional para producción, debes ser muy cuidadoso.
        Schema::table('appointments', function (Blueprint $table) {
             // Intentar revertir la FK
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    $table->dropForeign($this->foreignKeyName); // Usa el nombre guardado o inferido
                } catch (\Exception $e) {
                     Log::warning("Rollback: No se pudo soltar FK '{$this->foreignKeyName}': " . $e->getMessage());
                }
            }

            // Esto fallará si hay datos NULL en user_id.
            // DB::statement("ALTER TABLE appointments MODIFY COLUMN `user_id` BIGINT UNSIGNED NOT NULL;");

            // Volver a añadir la FK original con onDelete('cascade')
            // DB::statement("
            //     ALTER TABLE appointments
            //     ADD CONSTRAINT `appointments_user_id_foreign` -- O el nombre original si lo conoces
            //     FOREIGN KEY (`user_id`)
            //     REFERENCES `users` (`id`)
            //     ON DELETE CASCADE;
            // ");

            // Revertir los otros cambios de columna si los incluiste en el up() de ESTA migración.
        });
    }
};
