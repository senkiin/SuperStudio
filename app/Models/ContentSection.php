<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSection extends Model
{
     use HasFactory;

    protected $fillable = [
        'identifier',
        'main_title',
        'subtitle',
        'content_text',
    ];
}
