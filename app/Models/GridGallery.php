<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GridGallery extends Model
{
    use HasFactory;
    protected $fillable = ['identifier', 'title', 'description'];

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'grid_gallery_photo')
                    ->withPivot('order')
                    ->orderBy('grid_gallery_photo.order', 'asc');
    }
}
