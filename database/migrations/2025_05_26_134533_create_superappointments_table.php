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
        Schema::create('superappointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Para usuarios registrados (opcional)
            $table->string('guest_name')->nullable(); // Nombre del cliente si no está registrado
            $table->string('email'); // Email del cliente (obligatorio)
            $table->string('phone')->nullable(); // Teléfono del cliente

            $table->string('primary_service_name'); // Nombre del servicio principal como string
            $table->json('additional_services')->nullable(); // Para guardar un array de strings de servicios adicionales

            $table->dateTime('appointment_datetime'); // Fecha y hora de la cita
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superappointments');
    }
};
