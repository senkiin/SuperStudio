<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'image_path',
        'order',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}
