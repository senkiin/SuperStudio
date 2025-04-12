<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Los álbumes creados por este usuario.
     * Relación Uno a Muchos: Un User tiene muchos Albums.
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Los pedidos realizados por este usuario (cliente).
     * Relación Uno a Muchos: Un User tiene muchos Orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Las citas reservadas por este usuario (cliente).
     * Relación Uno a Muchos: Un User tiene muchas Appointments.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Las ofertas asociadas a este usuario (a través de la tabla pivot user_offers).
     * Relación Muchos a Muchos: Un User puede tener muchas Offers y viceversa.
     */
    public function offers(): BelongsToMany
    {
        // Asume que la tabla pivot se llama 'user_offers'
        // Puedes añadir ->withTimestamps() si tienes created_at/updated_at en la pivot
        // Puedes añadir ->withPivot('received_at', 'accepted_at') si tienes esos campos
        return $this->belongsToMany(Offer::class, 'user_offers')
                    ->withPivot('received_at', 'accepted_at')
                    ->withTimestamps();
    }

    /**
     * Las fotos subidas por este usuario (opcional, si se usa 'uploaded_by').
     * Relación Uno a Muchos: Un User puede subir muchas Photos.
     */
    public function uploadedPhotos(): HasMany
    {
        // Asume que la clave foránea en la tabla 'photos' es 'uploaded_by'
        return $this->hasMany(Photo::class, 'uploaded_by');
    }

     /**
     * Los videos subidos por este usuario (opcional, si se usa 'uploaded_by').
     * Relación Uno a Muchos: Un User puede subir muchos Videos.
     */
    public function uploadedVideos(): HasMany
    {
        // Asume que la clave foránea en la tabla 'videos' es 'uploaded_by'
        return $this->hasMany(Video::class, 'uploaded_by');
    }
}
