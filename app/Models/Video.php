<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    protected $fillable = [
        'album_id',
        'file_path',
        'thumbnail_path',
        'uploaded_by',
        'title',
        'description',
        'duration',
        'resolution',
    ];

    /**
     * El álbum al que pertenece este video.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Video pertenece a un Album.
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * El usuario que subió este video (opcional).
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Video fue subido por un User.
     */
    public function uploader(): BelongsTo
    {
        // Especificamos la clave foránea porque no sigue la convención ('uploader_id')
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
