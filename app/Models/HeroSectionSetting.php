<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSectionSetting extends Model // Nuevo nombre de clase
{
    use HasFactory;

    protected $table = 'hero_section_settings'; // Especificar el nombre de la tabla si difiere de la convención pluralizada

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'background_image_url',
    ];
}
