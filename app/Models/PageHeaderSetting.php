<?php
// app/Models/PageHeaderSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageHeaderSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier', // Importante para la lógica de firstOrCreate
        'hero_title',
        'hero_subtitle',
        'background_image_url',
    ];
}
