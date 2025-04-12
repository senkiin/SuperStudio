<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    /** @use HasFactory<\Database\Factories\AlbumFactory> */
    use HasFactory;
    

    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'user_id',
        'type',
        'client_id',
    ];

     /**
     * El usuario que creó/posee este álbum.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Album pertenece a un User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Las fotos contenidas en este álbum.
     * Relación Uno a Muchos: Un Album tiene muchas Photos.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Los videos contenidos en este álbum.
     * Relación Uno a Muchos: Un Album tiene muchos Videos.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

}
