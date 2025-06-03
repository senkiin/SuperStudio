<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Superappointment extends Model
{
    use HasFactory; // Si planeas usar factories

    protected $table = 'superappointments'; // Especifica el nombre de la tabla si no sigue la convención

    protected $fillable = [
        'user_id',
        'guest_name',
        'email',
        'phone',
        'primary_service_name',
        'additional_services', // Array de strings
        'appointment_datetime',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'additional_services' => 'array', // Importante: Laravel casteará esto a y desde JSON
    ];

    /**
     * El usuario (registrado) al que pertenece esta cita (opcional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // No necesitas una relación a ServiceType si guardas los nombres como strings
}
