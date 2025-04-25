<?php
// app/Models/ContentCard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link_url',
        'link_text',
        'order_column',
    ];

    protected $attributes = [
        'link_text' => 'Saber Más',
        'order_column' => 0,
    ];
}
