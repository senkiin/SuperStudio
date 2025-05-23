<?php
// app/Models/CarouselSlide.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarouselSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'background_image_path',
        'text_color',
        'text_animation',
        'button_text',
        'button_link',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
