<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Especifica el nombre de la tabla si no sigue la convención plural de Laravel.
     *
     * @var string
     */
    protected $table = 'google_reviews';

    /**
     * The attributes that are mass assignable.
     * Define qué columnas pueden ser llenadas masivamente al crear o actualizar.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'google_review_id',     // ID de la reseña en Google (para evitar duplicados)
        'reviewer_name',        // Nombre de quien hizo la reseña
        'reviewer_photo_url',   // URL de la foto de perfil (opcional)
        'rating',               // Puntuación (estrellas) como número entero (1-5)
        'review_text',          // El texto de la reseña
        'review_time',          // Cuándo se publicó la reseña en Google
        'is_visible',           // Booleano para controlar si se muestra en tu web
    ];

    /**
     * The attributes that should be cast.
     * Define cómo tratar ciertos tipos de datos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',          // Asegura que la puntuación sea un número
        'review_time' => 'datetime',    // Convierte la fecha/hora a un objeto Carbon
        'is_visible' => 'boolean',      // Convierte a booleano (true/false)
    ];

    /**
     * Los valores por defecto para los atributos.
     * Opcional: Define valores por defecto si no se proporcionan al crear.
     *
     * @var array
     */
    // protected $attributes = [
    //     'is_visible' => true,
    // ];

    // Aquí podrías añadir relaciones si fueran necesarias, por ejemplo:
    // public function location() {
    //     // Si tuvieras un modelo Location y guardaras location_id en google_reviews
    //     return $this->belongsTo(Location::class);
    // }
}
