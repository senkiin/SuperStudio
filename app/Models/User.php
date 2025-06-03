<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Session; // Necesario para el método isImpersonating

class User extends Authenticatable implements MustVerifyEmail
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
    /**
     * Las fotos a las que este usuario le ha dado "like".
     */
    public function likedPhotos(): BelongsToMany
    {
        // Nombre de la tabla pivot, clave foránea de este modelo, clave foránea del otro modelo
        return $this->belongsToMany(Photo::class, 'photo_user_likes', 'user_id', 'photo_id')
                    ->withTimestamps(); // Para que maneje created_at en la tabla pivot si lo tienes
    }
    public function likedPhoto(): BelongsToMany
    {
        // Nombre de la tabla pivot, clave foránea de este modelo, clave foránea del otro modelo
        return $this->belongsToMany(Photo::class, 'photo_user_likes', 'user_id', 'photo_id')
                    ->withTimestamps(); // Para que maneje created_at en la tabla pivot si lo tienes
    }
   /**
     * Determina si este usuario (el admin) tiene permiso para impersonar a otros.
     */
    public function canImpersonateOthers(): bool
    {
        // Lógica simple: Solo los usuarios con rol 'admin' pueden
        return $this->role === 'admin';
    }

    /**
     * Determina si este usuario puede ser impersonado por el usuario actual (admin).
     * Puedes pasar el $impersonator si necesitas lógica más compleja.
     */
    public function canBeImpersonated(): bool
    {
        // Lógica simple: Los administradores no pueden ser impersonados
        return $this->role !== 'admin';
    }

    /**
     * Verifica si la sesión actual es una sesión de impersonación.
     * Helper para usar en vistas/middleware.
     */
    public static function isImpersonating(): bool
    {
        return Session::has('original_admin_id');
    }

    /**
     * Obtiene el ID del administrador original si se está impersonando.
     */
    public static function getOriginalAdminId(): ?int
    {
        return Session::get('original_admin_id');
    }


}
