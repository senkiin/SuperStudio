<?php // Archivo: app/Models/GoogleReview.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleReview extends Model
{
    use HasFactory;

    protected $table = 'google_reviews';

    // Asegúrate de que todos los campos que usas en create() o updateOrCreate() estén aquí
    protected $fillable = [
        'author_name',
        'author_url',
        'language',
        'profile_photo_url',
        'rating',
        'relative_time_description',
        'text',
        'review_time',
        'translated',
        'is_visible',
    ];

    protected $casts = [
        'review_time' => 'datetime',
        'rating' => 'integer',
        'translated' => 'boolean',
        'is_visible' => 'boolean',
    ];

    // Valor por defecto para 'is_visible' al crear nuevos modelos
    protected $attributes = [
         'is_visible' => true,
    ];
}
