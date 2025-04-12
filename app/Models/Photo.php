<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'album_id',
        'file_path',
        'thumbnail_path',
        'uploaded_by',
        'like',
    ];

    /**
     * El álbum al que pertenece esta foto.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Una Photo pertenece a un Album.
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * El usuario que subió esta foto (opcional).
     * Relación Inversa Uno a Muchos (Muchos a Uno): Una Photo fue subida por un User.
     */
    public function uploader(): BelongsTo
    {
        // Especificamos la clave foránea porque no sigue la convención ('uploader_id')
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
