<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuratedGallery extends Model
{
    use HasFactory;
    protected $fillable = ['identifier', 'title'];

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'curated_gallery_photo')->withPivot('order')->orderBy('pivot_order');
    }
}
