<?php
// app/Models/InfoBlock.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'link_url',
        'link_text',
        'image_position',
        'order_column',
    ];

     // Optional: Define default values if needed
     protected $attributes = [
        'link_text' => 'Saber Más',
        'image_position' => 'left',
        'order_column' => 0,
    ];
}
