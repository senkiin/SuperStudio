<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_section_id',
        'entry_title',
        'entry_description',
        'video_source_type',
        'source_identifier',
        'thumbnail_url',
        'order_column',
    ];

    public function videoSection(): BelongsTo
    {
        return $this->belongsTo(VideoSection::class);
    }
}
