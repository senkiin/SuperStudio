<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

     /**
     * Las citas asociadas a este tipo de servicio.
     * Relación Uno a Muchos: Un ServiceType puede tener muchas Appointments.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

}
