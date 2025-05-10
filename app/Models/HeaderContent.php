<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_identifier',
        'title',
        'subtitle',
        'background_image_path',
        'button_text',
        'button_link',
    ];
}
