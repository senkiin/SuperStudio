<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blog_category_id',
        'title',
        'slug',
        'content',
        // 'featured_image_path', <-- Eliminado
        'video_url',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Nueva relación con las imágenes
    public function images(): HasMany
    {
        return $this->hasMany(BlogPostImage::class)->orderBy('order');
    }

    // Un "accesor" para obtener fácilmente la primera imagen (útil para la portada)
    public function getFirstImageUrlAttribute()
    {
        $firstImage = $this->images->first();
        if ($firstImage) {
            return Storage::disk('blog-media')->url($firstImage->image_path);
        }
        // Devuelve una imagen por defecto si no hay ninguna
        return 'https://via.placeholder.com/800x600.png?text=No+Image';
    }

    // ... (resto de relaciones: category, author, comments, likes se mantienen igual) ...

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->orderBy('created_at', 'desc');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(BlogLike::class);
    }
}
