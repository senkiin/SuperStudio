<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlbumSectionConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'section_title',
        'selected_album_ids_ordered',
    ];

    protected $casts = [
        'selected_album_ids_ordered' => 'array', // Laravel casteará automáticamente a/desde JSON
    ];
}
