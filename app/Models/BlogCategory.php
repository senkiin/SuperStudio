<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Asegúrate de que esta línea exista

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Define la relación "uno a muchos" con los posts del blog.
     * Una categoría puede tener muchos posts.
     */
    public function posts(): HasMany
    {
        // El nombre de esta función "posts" debe coincidir con lo que usas en withCount('posts')
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }
}
