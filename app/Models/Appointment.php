<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_name', // Added
        'email',      // Added
        'phone',
        'service_type_id',
        'status',
        'notes',
        'appointment_date',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    /**
     * El usuario (cliente) que reservó esta cita.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Appointment pertenece a un User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El tipo de servicio para esta cita.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Appointment pertenece a un ServiceType.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

}
